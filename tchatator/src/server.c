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
int menu_connexion(int cnx, ConfigSocketMessages config, int *compte, PGconn *conn);  // Menu de connexion
int menu_principal(int cnx, int compte, int id, PGconn *conn);  // Menu principal (choix des actions possibles)

int menu_listePro(int cnx, int id, PGconn *conn);  // menu de la liste des pros à contacter (renvoie l'id du pro à contacter)

int menu_conversation(int cnx, bool compte, int id_c, int id, PGconn *conn);

int menu_envoyerMessage(int cnx, bool compte, int id_c, int id, PGconn *conn);

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

        id = menu_connexion(cnx, configSocket, &compte, conn);

        if (compte != 0) {
            menu_principal(cnx, compte, id, conn);
        }
        
        close(cnx);
    }
    


    // Fermeture du socket
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
int menu_connexion(int cnx, ConfigSocketMessages config, int *compte, PGconn *conn) {
    PGresult *res;
    PGresult *res2;
    PGresult *res3;
    char buff[50];
    char query[256];

    int id;

    bool quitter = false;

    // Menu de connexion
    char menu[250] = 
        "+-------------------------------------+\n"
        "|            Se connecter             |\n"
        "+-------------------------------------+\n"
        "| [-1] Quitter                        |\n"
        "+-------------------------------------+\n"
        "> Entrez votre clé API : ";

    while ((*compte == 0) && (quitter == false)) {
        write(cnx, menu, strlen(menu));
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
        } else if (PQntuples(res2) > 0) {
            *compte = 2; // Utilisateur professionnel (public)
            id = atoi(PQgetvalue(res2, 0, PQfnumber(res, "id_c")));
        } else if (PQntuples(res3) > 0) {
            *compte = 2; // Utilisateur professionnel (privee)
            id = atoi(PQgetvalue(res3, 0, PQfnumber(res, "id_c")));
        } else if (strcmp(buff, config.cle_api_admin) == 0) { // Se connecter en tant qu'administrateur
            *compte = 3; // Utilisateur administrateur
        } else if (strcmp(buff, "-1") == 0) { // Se déconnecter
            quitter = true;
            close(cnx);
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

    return id;
}

int menu_principal(int cnx, int compte, int id, PGconn *conn) {
    char affichage[500];
    char menu[500];
    char buff[50];
    int id_c = -1;
    int ret;

    if (compte == 1) { // Utilisateur membre
        strcpy(menu,"+-------------------------------------+\n"
                    "|          Tchatator Membre           |\n"
                    "+-------------------------------------+\n"
                    "| [1] Voir les messages non lus       |\n"
                    "| [2] Voir ma conversation avec       |\n"
                    "|     un professionnel                |\n"
                    "| [3] Contacter un nouveau            |\n"
                    "|     professionnel                   |\n"
                    "| [-1] Quitter                        |\n"
                    "+-------------------------------------+\n");

    } else if (compte == 2) { // Utilisateur professionnel
        strcpy(menu,"+-------------------------------------+\n"
                    "|       Tchatator professionnel       |\n"
                    "+-------------------------------------+\n"
                    "| [1] Voir les messages non lus       |\n"
                    "| [2] Voir ma conversation avec       |\n"
                    "|     un membre                       |\n"
                    "| [-1] Quitter                        |\n"
                    "+-------------------------------------+\n");

    } else if (compte == 3) { // Utilisateur administrateur
        strcpy(menu,"+-------------------------------------+\n"
                    "|      Tchatator Administrateur       |\n"
                    "+-------------------------------------+\n"
                    "| [1] Bloquer un utilisateur          |\n"
                    "| [2] Bannir un utilisateur           |\n"
                    "| [-1] Quitter                        |\n"
                    "+-------------------------------------+\n");
    } else {
        return -1; // Utilisateur non connecté
    }

    strcpy(affichage, menu);
    strcat(affichage, "> Entrez votre choix : ");

    
    bool quitter = false;
    while (quitter == false) {
        write(cnx, affichage, strlen(affichage));
    
        int len = read(cnx, buff, sizeof(buff) - 1);

        buff[strcspn(buff, "\r\n")] = 0;
        buff[len] = '\0';

        if (len < 0) {
            perror("Erreur lors de la lecture");
            return -1;
        }

        // Liste des differents choix selon le compte :
        
        // Pour un membre
        if (compte == 1) {

            switch (atoi(buff)) {
                case 1:  // Si il choisit de voir ses messages non lus (membre)
                    /* TODO voir les messages non lus (membre) */
                    break;
                
                case 2:  // Si il choisit de voir une conversation déjà entamée (membre)
                    /* TODO voir ma conversation avec un pro (membre) */
                    break;

                case 3:  // Si il choisit d'envoyer un message
                    id_c = -1;
                    id_c = menu_listePro(cnx, id, conn);
                    
                    ret = menu_conversation(cnx, compte, id_c, id, conn);
                    if (ret == -1) {
                        return -1;
                    }
                    break;

                case -1:  // Se déconnecter
                    quitter = true;
                    close(cnx);
                    break;
                
                default:  // Choix non valide
                    strcpy(affichage, menu);
                    strcat(affichage, "Choix non valide, réessayez : ");
                    break;
            }

            strcpy(buff, "");
        }

        // Pour un professionnel
        else if (compte == 2) {

            switch (atoi(buff)) {
                case 1:  // Si il choisit de voir ses messages non lus (pro)
                    /* TODO voir les messages non lus (pro) */
                    break;
                
                case 2:  // Si il choisit de voir une conversation déjà entamée (pro)
                    /* TODO voir ma conversation avec un membre (pro) */
                    break;

                case -1:  // Se déconnecter
                    quitter = true;
                    close(cnx);
                    break;
                
                default:  // Choix non valide
                    strcpy(affichage, menu);
                    strcat(affichage, "Choix non valide, réessayez : ");
                    break;
            }

        }
        
        // Pour un administrateur
        else if (compte == 3) {

            switch (atoi(buff)) {
                case 1:  // Si il choisit de bloquer un utilisateur (admin)
                    /* TODO bloquer un utilisateur (admin) */
                    break;

                case 2:  // Si il choisit de bannir un utilisateur (admin)
                    /* TODO bannir un utilisateur (admin) */
                    break;

                case -1:  // Se déconnecter
                    quitter = true;
                    close(cnx);
                    break;
                
                default:  // Choix non valide
                    strcpy(affichage, menu);
                    strcat(affichage, "Choix non valide, réessayez : ");
                    break;
            }

        } else {
            perror("Utilisateur non connecté sur le menu principal");
            close(cnx);
            return -1; // Utilisateur non connecté
        }
    }

    return 0;
}

int menu_listePro(int cnx, int id, PGconn *conn) {
    PGresult *res;
    char query[512]; // Buffer statique de taille fixe pour la requête
    int rows;
    char liste[2048];
    char buff[10];
    bool quitter = false;
    char ident[10];

    char demande[100] = "> Saisissez le numéro du professionnel à contacter : ";

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
        return -1; // Retourner une erreur
    }

    // Récupérer les noms des colonnes pour les utiliser dans l'affichage
    int rais_soc_col = PQfnumber(res, "raison_social");
    if (rais_soc_col == -1) {
        fprintf(stderr, "Les colonnes 'id' ou 'nom' sont introuvables dans le résultat\n");
        return -1;
    }

    rows = PQntuples(res);

    int id_col = PQfnumber(res, "id_c");

    while (quitter == false) {
        strcpy(liste,
                    "+-------------------------------------+\n"
                    "|          Tchatator Membre           |\n"
                    "|                                     |\n"
                    "| Liste des professionnels            |\n"
                    "+-------------------------------------+\n"
        );

        for (int i = 0; i < rows; i++)
        {
            char *rais_soc = PQgetvalue(res, i, rais_soc_col);

            // Formater chaque ligne avec snprintf
            char ligne[128]; // Tampon pour une ligne
            snprintf(ligne, sizeof(ligne), "|  %d - %s", i + 1, rais_soc);

            // Calculer l'espace vide pour aligner
            int longueur = snprintf(NULL, 0, "%d", i + 1); // Obtenir la longueur de `i+1`
            int espace_vide = 32 - strlen(rais_soc) - longueur;

            // Ajouter des espaces
            for (int j = 0; j < espace_vide; j++) {
                strcat(ligne, " ");
            }
            strcat(ligne, "|\n");

            // Ajouter la ligne formatée à la liste
            strcat(liste, ligne);
        }
        strcat(liste, "| [-1] Retour                         |\n"
                      "+-------------------------------------+\n");

        strcat(liste, demande);
        
        write(cnx, liste, strlen(liste));
        
        int len = read(cnx, buff, sizeof(buff) - 1);
        if (len < 0) {
            perror("Erreur lors de la lecture");
            return -1;
        }

        buff[len] = '\0';

        if (strcmp(buff, "-1") == 0) { // Retour au menu principal
            return -1;
        }
        
        if (atoi(buff) > rows) {
            strcpy(demande, "> La sélection ne correspond à aucun professionnel, réessayez : ");
        } else {
            quitter = true;
            char *tempo = PQgetvalue(res, atoi(buff) - 1, id_col);  // Choix - 1 car l'index commence à 0
            strcpy(ident, tempo);
        }
        
    }

    // Libérer les ressources
    PQclear(res);

    return atoi(ident);
}

int menu_envoyerMessage(int cnx, bool compte, int id_c, int id, PGconn *conn) {
    // TODO
    return -1;
}

int menu_conversation(int cnx, bool compte, int id_c, int id, PGconn *conn) {
    PGresult *res;
    int rais_soc_col;
    char buff[10];
    char query[512]; // Buffer statique pour la requête
    char selec[50] = "> Entrez votre choix : ";
    char menuChoix[500];
    char menu[500] = "+-------------------------------------+\n"
                     "|          Tchatator Membre           |\n"
                     "|                                     |\n"
                     "| Conversation avec ";

    // Construire la requête avec snprintf
    snprintf(query, sizeof(query),
        "SELECT p.raison_social "
        "FROM tripskell.pro_prive p "
        "WHERE p.id_c = %d "
        "UNION "
        "SELECT p.raison_social "
        "FROM tripskell.pro_public p "
        "WHERE p.id_c = %d", id_c, id_c);

    // Exécuter la requête
    res = PQexec(conn, query);

    // Vérifier si la requête a réussi
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Query execution failed: %s\n", PQerrorMessage(conn));
        PQclear(res);
        return -1; // Retourner une erreur
    }

    rais_soc_col = PQfnumber(res, "raison_social");
    
    char *rais_soc = PQgetvalue(res, 0, rais_soc_col);

    // Formater chaque ligne avec snprintf
    char ligne[128]; // Tampon pour une ligne
    snprintf(ligne, sizeof(ligne), "%s", rais_soc);

    // Calculer l'espace vide pour aligner
    int espace_vide = 18 - strlen(rais_soc);

    // Ajouter des espaces
    for (int j = 0; j < espace_vide; j++) {
        strcat(ligne, " ");
    }
    strcat(ligne, "|\n");
    strcat(menu, ligne);

    strcat(menu,"+-------------------------------------+\n"
                "| [1] Voir l'historique des messages  |\n"
                "| [2] Envoyer un message              |\n"
                "| [3] Supprimer un message            |\n"
                "| [4] Modifier  un message            |\n"
                "| [-1] Retour                         |\n"
                "+-------------------------------------+\n"
    );

    bool quitter = false;

    strcpy(menuChoix, menu);
    strcat(menuChoix, selec);

    while (!quitter) {
        write(cnx, menuChoix, strlen(menuChoix));

        int len = read(cnx, buff, sizeof(buff) - 1);
        if (len < 0) {
            perror("Erreur lors de la lecture");
            return -1;
        }

        buff[len] = '\0';

        switch (atoi(buff)) {
            case 1:
                // TODO : Afficher l'historique des messages
                break;
            case 2:
                // TODO : Envoyer un message
                break;
            case 3:
                // TODO : Supprimer un message
                break;
            case 4:
                // TODO : Modifier un message
                break;
            case -1:
                quitter = true;
                break;
            default:

                strcpy(selec, "> Choix non valide, réessayez : ");

                strcpy(menuChoix, menu);
                strcat(menuChoix, selec);
                break;
        }
    }
    

    return 0;
}