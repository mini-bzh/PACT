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

// Structure pour la configuration des messages et de la socket
typedef struct {
    int tailleMessMax;
    int mess_max_min;
    int mess_max_heure;
    int max_historique;
    int duree_bloquage_heure;
    int duree_ban_mois;
    char cle_api_admin[50];
    char fic_logs_path[30];
    int file_attente;
    int port;
} ConfigSocketMessages;

// Structure pour la configuration de la base de données
typedef struct {
    char server[30];
    char dbname[30];
    char user[30];
    char pass[30];
} ConfigBDD;

// Prototypes des fonctions
int lire_config(const char *filename, ConfigSocketMessages *configSocket, ConfigBDD *configBDD);  // Lit la configuration du server
PGconn *connect_to_db(ConfigBDD *configBDD);  // Connexion à la BDD
int create_socket(ConfigSocketMessages *configSocket);  // Crée un socket
void afficher_tout(PGresult *res);  // Fonction de debug pour afficher le resultat d'une requête

// Prototypes des menus
int menu_connexion(int cnx, bool *quitter, ConfigSocketMessages config, int *compte, PGconn *conn);  // Menu de connexion
int menu_principal(int cnx, int compte, bool *quitter);  // Menu principal (choix des actions possibles)

// Fonction principale
int main() {

    ConfigSocketMessages configSocket;
    ConfigBDD configBDD;

    int compte = 0;

    bool quitter = false;

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

    // Acceptation de la connexion
    printf("Acceptation de la connexion...\n");
    struct sockaddr_in conn_addr;
    int size = sizeof(conn_addr);
    int cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
    if (cnx < 0) {
        perror("Erreur lors de l'acceptation de la connexion");
        close(sock);
        PQfinish(conn);
        return -1;
    }

    // Boucle principale
    while (quitter == false)
    {
        menu_connexion(cnx, &quitter, configSocket, &compte, conn);

        if (compte != 0) {
            menu_principal(cnx, compte, &quitter);
            quitter = true;
        }
        
    }
    


    // Fermeture du socket et de la connexion
    close(cnx);
    close(sock);

    // Fermeture de la connexion à la BDD
    PQfinish(conn);

    return 0;
}

//////////////////////////////
//        FONCTIONS         //
//////////////////////////////

// Fonction pour lire le fichier de configuration
int lire_config(const char *filename, ConfigSocketMessages *configSocket, ConfigBDD *configBDD) {

    FILE *file;
    char line[256];

    // Ouvrir le fichier
    file = fopen(filename, "r");
    if (file == NULL) {
        fprintf(stderr, "Impossible d'ouvrir le fichier %s\n", filename);
        return -1;
    }

    // Lire chaque ligne du fichier
    while (fgets(line, sizeof(line), file)) {
        // Supprimer le caractère de fin de ligne si présent
        line[strcspn(line, "\n")] = 0;

        // Vérifier chaque clé et stocker la valeur dans la structure appropriée
        if (strncmp(line, "taille_message", strlen("taille_message")) == 0) {
            sscanf(line + strlen("taille_message") + 1, "%d", &configSocket->tailleMessMax);
        } else if (strncmp(line, "cle_api_admin", strlen("cle_api_admin")) == 0) {
            sscanf(line + strlen("cle_api_admin") + 1, "%s", configSocket->cle_api_admin);
        } else if (strncmp(line, "mess_max_min", strlen("mess_max_min")) == 0) {
            sscanf(line + strlen("mess_max_min") + 1, "%d", &configSocket->mess_max_min);
        } else if (strncmp(line, "mess_max_heure", strlen("mess_max_heure")) == 0) {
            sscanf(line + strlen("mess_max_heure") + 1, "%d", &configSocket->mess_max_heure);
        } else if (strncmp(line, "max_historique", strlen("max_historique")) == 0) {
            sscanf(line + strlen("max_historique") + 1, "%d", &configSocket->max_historique);
        } else if (strncmp(line, "duree_bloquage_heure", strlen("duree_bloquage_heure")) == 0) {
            sscanf(line + strlen("duree_bloquage_heure") + 1, "%d", &configSocket->duree_bloquage_heure);
        } else if (strncmp(line, "duree_ban_mois", strlen("duree_ban_mois")) == 0) {
            sscanf(line + strlen("duree_ban_mois") + 1, "%d", &configSocket->duree_ban_mois);
        } else if (strncmp(line, "fic_logs_path", strlen("fic_logs_path")) == 0) {
            sscanf(line + strlen("fic_logs_path") + 1, "%s", configSocket->fic_logs_path);
        } else if (strncmp(line, "file_attente", strlen("file_attente")) == 0) {
            sscanf(line + strlen("file_attente") + 1, "%d", &configSocket->file_attente);
        } else if (strncmp(line, "port", strlen("port")) == 0) {
            sscanf(line + strlen("port") + 1, "%d", &configSocket->port);
        } else if (strncmp(line, "server", strlen("server")) == 0) {
            sscanf(line + strlen("server") + 1, "%s", configBDD->server);
        } else if (strncmp(line, "dbname", strlen("dbname")) == 0) {
            sscanf(line + strlen("dbname") + 1, "%s", configBDD->dbname);
        } else if (strncmp(line, "user", strlen("user")) == 0) {
            sscanf(line + strlen("user") + 1, "%s", configBDD->user);
        } else if (strncmp(line, "pass", strlen("pass")) == 0) {
            sscanf(line + strlen("pass") + 1, "%s", configBDD->pass);
        }
    }

    fclose(file);
    return 0;
}

// Fonction pour se connecter à la base de données
PGconn *connect_to_db(ConfigBDD *configBDD) {
    PGconn *conn;
    char conninfo[256];

    snprintf(conninfo, sizeof(conninfo), "host=%s dbname=%s user=%s password=%s",
             configBDD->server, configBDD->dbname, configBDD->user, configBDD->pass);

    conn = PQconnectdb(conninfo);

    if (PQstatus(conn) != CONNECTION_OK) {
        fprintf(stderr, "Échec de la connexion à la base de données : %s\n", PQerrorMessage(conn));
        PQfinish(conn);
        return NULL;
    }

    return conn;
}

// Fonction pour créer un socket et le gérer
int create_socket(ConfigSocketMessages *configSocket) {
    int sock;
    int ret;
    struct sockaddr_in addr;

    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock < 0) {
        perror("Erreur lors de la création du socket");
        return -1;
    }

    addr.sin_addr.s_addr = inet_addr("127.0.0.1");
    addr.sin_family = AF_INET;
    addr.sin_port = htons(configSocket->port);
    ret = bind(sock, (struct sockaddr *)&addr, sizeof(addr));
    if (ret < 0) {
        perror("Erreur lors du bind du socket");
        return -1;
    }

    ret = listen(sock, 1);
    if (ret < 0) {
        perror("Erreur lors de l'écoute du socket");
        return -1;
    }

    return sock;
}

// Fonction pour afficher le resultat d'une requête SQL
void afficher_tout(PGresult *res) {
    int nRows = PQntuples(res);  // Nombre de lignes
    int nCols = PQnfields(res); // Nombre de colonnes

    printf("\n");
    // Vérification si le résultat est vide
    if (nRows == 0) {
        const char *msg = "Aucun résultat à afficher.\r\n";
        printf(msg);
        return;
    }

    char buffer[1024]; // Tampon pour la sortie

    // Afficher les noms des colonnes
    for (int col = 0; col < nCols; col++) {
        snprintf(buffer, sizeof(buffer), "%s\t", PQfname(res, col));
        printf(buffer);
    }
    printf( "\r\n");

    // Afficher les valeurs de chaque ligne
    for (int row = 0; row < nRows; row++) {
        for (int col = 0; col < nCols; col++) {
            snprintf(buffer, sizeof(buffer), "%s\t", PQgetvalue(res, row, col));
            printf(buffer);
        }
        printf("\r\n");
    }
    printf("\n");
}




//////////////////////////////
//          MENUS           //
//////////////////////////////

// Fonction pour gérer le menu de connexion
int menu_connexion(int cnx, bool *quitter, ConfigSocketMessages config, int *compte, PGconn *conn) {
    PGresult *res;
    PGresult *res2;
    PGresult *res3;
    char buff[50];
    char query[256];

    // Menu de connexion
    char menu[250] = 
        "+-------------------------------------+\n"
        "|            Se connecter             |\n"
        "+-------------------------------------+\n"
        "| [-1] Quitter                        |\n"
        "+-------------------------------------+\n"
        "> Entrez votre clé API : ";

    while ((*compte == 0) && (*quitter == false)) {
        write(cnx, menu, strlen(menu));
        int len = read(cnx, buff, sizeof(buff) - 1);
        if (len < 0) {
            perror("Erreur lors de la lecture");
            return -1;
        }

        buff[strcspn(buff, "\r\n")] = 0;

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
            write(cnx, "Connexion réussie\n", strlen("Connexion réussie\n"));
        } else if ((PQntuples(res2) > 0) || (PQntuples(res3) > 0)) {
            *compte = 2; // Utilisateur professionnel
            write(cnx, "Connexion réussie\n", strlen("Connexion réussie\n"));
        } else if (strcmp(buff, config.cle_api_admin) == 0) { // Se connecter en tant qu'administrateur
            *compte = 3; // Utilisateur administrateur
            write(cnx, "Connexion réussie\n", strlen("Connexion réussie\n"));
        } else if (strcmp(buff, "-1") == 0) { // Se déconnecter
            write(cnx, "Fin de la connexion\n", strlen("Fin de la connexion\n"));
            *quitter = true;
        } else {  // Clé API incorrecte
            strcpy(menu,
            "+-------------------------------------+\n"
            "|            Se connecter             |\n"
            "+-------------------------------------+\n"
            "| [-1] Quitter                        |\n"
            "+-------------------------------------+\n"
            "> Clé API incorrecte, réessayez : ");
        }
    }

    PQclear(res);
    PQclear(res2);
    PQclear(res3);
    return 0;
}

int menu_principal(int cnx, int compte, bool *quitter) {
    char menu[350];
    char buff[50];

    if (compte == 1) { // Utilisateur membre
        strcpy(menu,"+-------------------------------------+\n"
                    "|          Tchatator Membre           |\n"
                    "+-------------------------------------+\n"
                    "| [1] Voir les messages non lus       |\n"
                    "| [2] Voir ma conversation avec       |\n"
                    "|     un professionnel                |\n"
                    "| [-1] Quitter                        |\n"
                    "+-------------------------------------+\n"
                    "> Entrez votre choix : ");

    } else if (compte == 2) { // Utilisateur professionnel
        strcpy(menu,"+-------------------------------------+\n"
                    "|       Tchatator professionnel       |\n"
                    "+-------------------------------------+\n"
                    "| [1] Voir les messages non lus       |\n"
                    "| [2] Voir ma conversation avec       |\n"
                    "|     un professionnel                |\n"
                    "| [-1] Quitter                        |\n"
                    "+-------------------------------------+\n"
                    "> Entrez votre choix : ");

    } else if (compte == 3) { // Utilisateur administrateur
        strcpy(menu,"+-------------------------------------+\n"
                    "|      Tchatator Administrateur       |\n"
                    "+-------------------------------------+\n"
                    "| [1] Bloquer un utilisateur          |\n"
                    "| [2] Bannir un utilisateur           |\n"
                    "| [-1] Quitter                        |\n"
                    "+-------------------------------------+\n"
                    "> Entrez votre choix : ");
    } else {
        return -1; // Utilisateur non connecté
    }

    write(cnx, menu, strlen(menu));

    int len = read(cnx, buff, sizeof(buff) - 1);
    if (len < 0) {
        perror("Erreur lors de la lecture");
        return -1;
    }

    if (strcmp(buff, "-1") == 0) { // Se déconnecter
        write(cnx, "Fin de la connexion\n", strlen("Fin de la connexion\n"));
        *quitter = true;
    }

    return 0;
}

