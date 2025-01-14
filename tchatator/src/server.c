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
int lire_config(const char *filename, ConfigSocketMessages *configSocket, ConfigBDD *configBDD);
PGconn *connect_to_db(ConfigBDD *configBDD);
int create_socket(ConfigSocketMessages *configSocket);

// Prototypes des menus
int menu_connexion(int cnx, bool *quitter, ConfigSocketMessages config, int *compte);
int menu_principal();

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
    if (!conn) {
        return -1;
    }
    PGresult *res;


    // Création du socket
    printf("Création du socket...\n");
    int sock = create_socket(&configSocket);
    if (sock < 0) {
        PQfinish(conn);
        return -1;
    }

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
        menu_connexion(cnx, &quitter, configSocket, &compte);
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

int menu_connexion(int cnx, bool *quitter, ConfigSocketMessages config, int *compte) {
    char buff[50];
    char menu[250] = 
        "+-------------------------------------+\n"
        "|            Se connecter             |\n"
        "+-------------------------------------+\n"
        "| [-1] Quitter                        |\n"
        "+-------------------------------------+\n"
        "> Entrez votre clé API : ";

    while ((*compte == 0) && (*quitter == false)) {
        write(cnx, config.cle_api_admin, strlen(config.cle_api_admin));
        write(cnx, menu, strlen(menu));
        int len = read(cnx, buff, sizeof(buff) - 1);
        if (len < 0) {
            perror("Erreur lors de la lecture");
            return -1;
        }

        buff[len] = '\0'; // Ajout de la terminaison

        char texte_ajoute[] = "\r\n";
        char cle_combinee[60];

        snprintf(cle_combinee, sizeof(cle_combinee), "%s%s", config.cle_api_admin, texte_ajoute);      

        if (strcmp(buff, cle_combinee) == 0) {
            *compte = 1; // Utilisateur administrateur
            write(cnx, "Connexion réussie\n", strlen("Connexion réussie\n"));
        } else if (strcmp(buff, "-1\r\n") == 0) {
            write(cnx, "Fin de la connexion\n", strlen("Fin de la connexion\n"));
            *quitter = true;
        } else {
            strcpy(menu,
            "+-------------------------------------+\n"
            "|            Se connecter             |\n"
            "+-------------------------------------+\n"
            "| [-1] Quitter                        |\n"
            "+-------------------------------------+\n"
            "> Clé API incorrecte, réessayez : ");
        }
    }

    return 0;
}

int menu_principal() {


    return 0;
}

