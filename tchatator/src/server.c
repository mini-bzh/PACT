#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <stdio.h>
#include <string.h>
#include <libpq-fe.h>
#include <stdbool.h>
#include <stdlib.h>

#include "types.h"
#include "prototypes.h"

bool verbose = false;

void push_log(char* mess) {
    char commande[512];
    if (verbose) {
        printf("%s", mess);
    }
    sprintf(commande, "echo '%s' >> ../.logs/server.log", mess);
    system(commande);
}

int main( int argc, char **argv ) {

    if (argc==2) {
        if (strcmp(argv[1], "--help") == 0) {
            printf("Utilisation: %s [--help]\n", argv[0]);
            printf("Options:\n");
            printf("  --help : affiche cette aide\n");
            printf("  --verbose : affiche les logs\n");
            return 0;
        } else if (strcmp(argv[1], "--verbose") == 0) {
            verbose = true;
        } else {
            printf("Option inconnue: %s\n", argv[1]);
            return 1;
        }
    }

    ConfigSocketMessages configSocket;
    ConfigBDD configBDD;

    int compte = 0;
    int id;
    char buffer[500];

    bool deco;

    system("clear");

    push_log("Lecture de la configuration...\n");
    
    if (lire_config("../.config/config.txt", &configSocket, &configBDD) != 0) {
        return -1;
    }

    // Connection à la BDD
    push_log("Connexion à la base de données...\n");

    PGconn *conn = connect_to_db(&configBDD);
    if (PQstatus(conn) != CONNECTION_OK) {
        fprintf(stderr, "Connection failed: %s\n", PQerrorMessage(conn));
        PQfinish(conn);
        exit(EXIT_FAILURE);
    }

    // Création du socket
    push_log("Création du socket...\n");

    int sock = create_socket(&configSocket);
    if (sock < 0) {
        PQfinish(conn);
        return -1;
    }

    char *dbname = PQdb(conn);

    push_log("Connecté à la base de données :");
    push_log(dbname);
    push_log("\n");

    struct sockaddr_in conn_addr;
    int size = sizeof(conn_addr);

    // Boucle principale
    while (true) {
        id = -1;
        compte = 0;
        deco = true;

        // Acceptation de la connexion
        push_log("En attente de connexion...\n");
        int cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
        if (cnx < 0) {
            perror("Erreur lors de l'acceptation de la connexion");
            close(sock);
            PQfinish(conn);
            return -1;
        }

        char cnx_char[12];
        sprintf(cnx_char, "%d", cnx);
        push_log("Connexion réussi au CLIENT : ");
        push_log(cnx_char);
        push_log("\n");

        write(cnx, "200", 3); // envoie code 200

        id = identification(cnx, configSocket, &compte, conn);
        if (id != -1) {
            deco = false;
            char id_char[12];
            char compte_char[12];
            sprintf(id_char, "%d", id);
            sprintf(compte_char, "%d", compte);
            push_log("Identification réussi : (id : ");
            push_log(id_char);
            push_log(",type compte : ");
            push_log(compte_char);
            push_log(")\n");
        }
        
        while (!deco) {  // Si l'utilisateur est connecté, on traite les requêtes jusqu'à la déconnexion
            read(cnx, buffer, sizeof(buffer));
            if(verbose) {
                push_log("requete: ");
                push_log(get_json_value(buffer, "requete"));
                push_log("\n");
            }
            if (strcmp(get_json_value(buffer, "requete"), "liste_pro") == 0) {
                reponse_liste_pro(cnx, configSocket, conn, id, buffer);
            } else if (strcmp(get_json_value(buffer, "requete"), "liste_membre") == 0) {
                reponse_liste_membre(cnx, configSocket, conn, id);
            } else if (strcmp(get_json_value(buffer, "requete"), "deconnexion") == 0) {
                write(cnx,"{\"reponse\":\"402\"}", utf8_strlen("{\"reponse\":\"402\"}"));
                deco = true;
            } else if (strcmp(get_json_value(buffer, "requete"), "send_mess") == 0) {
                send_mess(cnx, configSocket, conn, id, buffer);
            } else if (strcmp(get_json_value(buffer, "requete"), "historique_mess") == 0) {
                historique_mess(cnx, configSocket, conn, id, buffer);
            } else if (strcmp(get_json_value(buffer, "requete"), "mess_non_vu") == 0) {
                envoie_mess_non_lu(cnx, id, conn);
            } else if (strcmp(get_json_value(buffer, "requete"), "modif_mess") == 0) {
                modif_mess(cnx, conn, atoi(get_json_value(buffer, "id_compte")), id, atoi(get_json_value(buffer, "id_message")));
            } else if (strcmp(get_json_value(buffer, "requete"), "sup_mess") == 0) {
                sup_mess(cnx, conn, atoi(get_json_value(buffer, "id_compte")), id, atoi(get_json_value(buffer, "id_message")));
            }
            memset(buffer, 0, sizeof(buffer));
        }
        push_log("\n");

        close(cnx);
        if(verbose) {
            printf("Deconnexion réussi du CLIENT : %d\n\n", cnx);
        }
    }
    


    // Fermeture du socket
    close(sock);

    // Fermeture de la connexion à la BDD
    PQfinish(conn);

    return 0;
}
