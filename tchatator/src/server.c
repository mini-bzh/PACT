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

// Fonction pour extraire la valeur associée à une clé dans un JSON
char* get_json_value(const char* json, const char* key) {
    char* key_pattern = (char*)malloc(strlen(key) + 4); // +4 pour les guillemets et le ":"
    sprintf(key_pattern, "\"%s\":", key); // Format: "key":

    // Recherche de la clé dans le JSON
    char* key_pos = strstr(json, key_pattern);
    free(key_pattern);

    if (!key_pos) {
        return NULL; // Clé non trouvée
    }

    // Pointeur vers le début de la valeur
    char* value_start = key_pos + strlen(key) + 3; // Déplace le pointeur après la clé et les ":"
    while (*value_start == ' ' || *value_start == '\t' || *value_start == '\n') {
        value_start++; // Ignore les espaces blancs
    }

    // Déterminer si la valeur est une chaîne ou un tableau
    if (*value_start == '"') { // Valeur est une chaîne
        value_start++; // Déplace le pointeur après le guillemet
        char* value_end = strchr(value_start, '"'); // Trouve le guillemet de fin
        if (!value_end) {
            return NULL; // Format invalide
        }
        size_t length = value_end - value_start;
        char* value = (char*)malloc(length + 1); // +1 pour le caractère nul
        strncpy(value, value_start, length);
        value[length] = '\0';
        return value;
    } else if (*value_start == '[') { // Valeur est un tableau
        value_start++; // Déplace le pointeur après le '['
        char* value_end = strchr(value_start, ']'); // Trouve le ']' de fin
        if (!value_end) {
            return NULL; // Format invalide
        }
        size_t length = value_end - value_start;
        char* value = (char*)malloc(length + 3); // +3 pour les crochets et le caractère nul
        sprintf(value, "[%.*s]", (int)length, value_start);
        return value;
    }

    return NULL; // Format non supporté
}

int identification(int cnx, ConfigSocketMessages config, int *compte, PGconn *conn) {
    PGresult *res;
    PGresult *res2;
    PGresult *res3;
    char buff[50];
    char query[256];

    int id;

    bool quitter = false;

    while ((*compte == 0) && (quitter == false)) {
        int len = read(cnx, buff, sizeof(buff) - 1);
        if (len < 0) {
            perror("Erreur lors de la lecture");
            return -1;
        }

        buff[strcspn(buff, "\r\n")] = 0;
        buff[len] = '\0';

        // Construire la requête SQL avec une variable
        snprintf(query, sizeof(query), "SELECT * FROM tripskell.membre WHERE membre.clefAPI = '%s';", buff);
        res = PQexec(conn, query); // Exécuter la requête SQL d'un membre

        snprintf(query, sizeof(query), "SELECT * FROM tripskell.pro_public WHERE pro_public.clefAPI = '%s';", buff);
        res2 = PQexec(conn, query); // Exécuter la requête SQL d'un professionnel public

        snprintf(query, sizeof(query), "SELECT * FROM tripskell.pro_prive WHERE pro_prive.clefAPI = '%s';", buff);
        res3 = PQexec(conn, query); // Exécuter la requête SQL d'un professionnel privee
        
        if ((PQresultStatus(res) != PGRES_TUPLES_OK) || (PQresultStatus(res2) != PGRES_TUPLES_OK) || (PQresultStatus(res3) != PGRES_TUPLES_OK)) {
            fprintf(stderr, "Échec de l'exécution de la requête : %s\n", PQerrorMessage(conn));
            PQclear(res);
            PQclear(res2);
            PQclear(res3);
            return -1;
        }


        if (PQntuples(res) > 0)
        {
            *compte = 1; // Utilisateur membre
            id = atoi(PQgetvalue(res, 0, PQfnumber(res, "id_c")));
            write(cnx, "200", 3); // envoie code 200
        } else if (PQntuples(res2) > 0) {
            *compte = 2; // Utilisateur professionnel (public)
            id = atoi(PQgetvalue(res2, 0, PQfnumber(res, "id_c")));
            write(cnx, "200", 3); // envoie code 200
        } else if (PQntuples(res3) > 0) {
            *compte = 2; // Utilisateur professionnel (privee)
            id = atoi(PQgetvalue(res3, 0, PQfnumber(res, "id_c")));
            write(cnx, "200", 3); // envoie code 200
        } else if (strcmp(buff, config.cle_api_admin) == 0) { // Se connecter en tant qu'administrateur
            *compte = 3; // Utilisateur administrateur
            write(cnx, "200", 3); // envoie code 200
        } else if (strcmp(buff, "-1") == 0) { // Se déconnecter
            quitter = true;
            
        } else {  // Clé API incorrecte
            write(cnx, "401", 3); // envoie code erreur
        }
    }

    PQclear(res);
    PQclear(res2);
    PQclear(res3);

    return id;
}

void reponse_liste_pro(int cnx, ConfigSocketMessages config, PGconn *conn, int id){
    PGresult *res;
    char query[512]; // Buffer statique de taille fixe pour la requête
    int rows;
    char liste[2048] = {0};


    // Construire la requête avec snprintf
    snprintf(query, sizeof(query),
        "SELECT p.id_c, p.raison_social "
        "FROM tripskell.pro_prive p "
        "WHERE p.id_c NOT IN ("
        "    SELECT m.idreceveur "
        "    FROM tripskell._message m "
        "    WHERE m.idenvoyeur = %d"
        ") "
        "UNION "
        "SELECT p.id_c, p.raison_social "
        "FROM tripskell.pro_public p "
        "WHERE p.id_c NOT IN ("
        "    SELECT m.idreceveur "
        "    FROM tripskell._message m "
        "    WHERE m.idenvoyeur = %d"
        ");", id, id);

    // Exécuter la requête
    res = PQexec(conn, query);

    // Vérifier si la requête a réussi
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Query execution failed: %s\n", PQerrorMessage(conn));
        PQclear(res);
        
    }

    // Récupérer les noms des colonnes pour les utiliser dans l'affichage
    int rais_soc_col = PQfnumber(res, "raison_social");
    if (rais_soc_col == -1) {
        fprintf(stderr, "Les colonnes 'id' ou 'nom' sont introuvables dans le résultat\n");
        
    }

    rows = PQntuples(res);

    char *rais_soc = PQgetvalue(res, 0, rais_soc_col);

    // Formater chaque ligne avec snprintf
    char ligne[128]; // Tampon pour une ligne
    snprintf(ligne, sizeof(ligne), "[\"%s\"",rais_soc);

    // Ajouter la ligne formatée à la liste
    strcat(liste, ligne);

    for (int i = 1; i < rows; i++)
    {
        char *rais_soc = PQgetvalue(res, i, rais_soc_col);

        // Formater chaque ligne avec snprintf
        char ligne[128]; // Tampon pour une ligne
        snprintf(ligne, sizeof(ligne), ",\"%s\"",rais_soc);

        // Ajouter la ligne formatée à la liste
        strcat(liste, ligne);
    }
    strcat(liste, "]");

    printf("liste %s\n", liste);

    char response[512] = "{";
    strcat(response,"\"state\":\"200\"");
    strcat(response,",\"data\":");strcat(response,liste);
    strcat(response,"}");
    write(cnx, response, strlen(response));
}

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
