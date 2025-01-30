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

// Fonction principale
int main() {

    ConfigSocketMessages configSocket;
    ConfigBDD configBDD;

    int compte = 0;
    int id;

    system("clear");

    printf("Lecture de la configuration...\n");
    if (lire_config("../.config/config.txt", &configSocket, &configBDD) != 0) {
        return -1;
    }

    // Connection à la BDD
    printf("Connexion à la base de données...\n");
    PGconn *conn = connect_to_db(&configBDD);
    if (PQstatus(conn) != CONNECTION_OK) {
        fprintf(stderr, "Connection failed: %s\n", PQerrorMessage(conn));
        PQfinish(conn);
        exit(EXIT_FAILURE);
    }

    // Création du socket
    printf("Création du socket...\n");
    int sock = create_socket(&configSocket);
    if (sock < 0) {
        PQfinish(conn);
        return -1;
    }

    char *dbname = PQdb(conn);
    printf("Connecté à la base de données : %s\n", dbname);

    struct sockaddr_in conn_addr;
    int size = sizeof(conn_addr);

    // Boucle principale
    while (true) {
        id = -1;
        compte = 0;

        // Acceptation de la connexion
        printf("Acceptation de la connexion...\n");
        int cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
        if (cnx < 0) {
            perror("Erreur lors de l'acceptation de la connexion");
            close(sock);
            PQfinish(conn);
            return -1;
        }

        printf("Connexion réussi au client : %d\n", cnx);

        write(cnx, "200", 3); // envoie code 200

        id = identification(cnx, configSocket, &compte, conn);

        printf("Identification réussi type compte : %d\n", compte);

        char type_comte_tosend[12];
        sprintf(type_comte_tosend, "%d", compte);

        write(cnx, type_comte_tosend, 1); // on envoie le type de compte utilisé

        //id = menu_connexion(cnx, configSocket, &compte, conn);

        /*if (compte != 0) {
            menu_principal(cnx, compte, id, conn);
        }*/
        bool done = false;
        char buf[500];
        while(!done) {
            read(cnx, buf, sizeof(buf) - 1);
            printf("requete: %s\n", get_json_value(buf, "requete"));
            if(strcmp(get_json_value(buf, "requete"), "liste_pro") == 0) {
                reponse_liste_pro(cnx, configSocket, conn, id);
            }
        }
        
        printf("\n");

        sleep(200);

        close(cnx);
    }
    


    // Fermeture du socket
    close(sock);

    // Fermeture de la connexion à la BDD
    PQfinish(conn);

    return 0;
}
