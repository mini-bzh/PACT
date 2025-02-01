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
#include <ctype.h>

#include "types.h"
#include "prototypes.h"


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

int afficher_message(int idMess, PGconn *conn, char *rais_soc, int cnx) {
    PGresult *res;
    char query[256];
    char blocMessage[556];

    snprintf(query, sizeof(query), "SELECT * FROM tripskell._message WHERE idmes = %d ORDER BY datecreation ASC;", idMess);

    res = PQexec(conn, query);
    if (PQresultStatus(res)!= PGRES_TUPLES_OK) {
        fprintf(stderr, "Échec de la requête : %s\n", PQerrorMessage(conn));
        PQclear(res);
        return -1;
    }

    int ind_date = PQfnumber(res, "datecreation");
    int ind_mess = PQfnumber(res, "contentmessage");

    char *dateMess = PQgetvalue(res, 0, ind_date);

    char *messContent = PQgetvalue(res, 0, ind_mess);
    messContent[strlen(messContent)] = '\0';
    
    wrap_text(messContent, 62);

    // Trouver le premier espace dans la chaîne
    char *spacePos = strchr(dateMess, ' ');
    if (spacePos != NULL) {
        *spacePos = '\0'; // Remplacer l'espace par '\0' pour couper la chaîne
    }

    changer_format_date(dateMess);

    strcpy(blocMessage, "");
    snprintf(blocMessage, sizeof(blocMessage), "Message %d : %s - %s\n", 1, rais_soc, dateMess);

    strcat(blocMessage, messContent);

    write(cnx, blocMessage, strlen(blocMessage));
    
    PQclear(res);

    return 1;
}

void wrap_text(char *input, int line_length) {
    char text[2000];
    char line[100];
    int lenInput = utf8_strlen(input);
    int start = 0;
    int end = -1;

    bool finForme = false;
    
    strcpy(text, "+----------------------------------------------------------------+\n");
    while (finForme == false) {

        end = start + line_length-1;
        
        utf8_strncpy(line, &input[start], line_length);
        
        line[utf8_strlen(line)] = '\0';

        if (end < lenInput) {
            while (line[end] != ' ') {
                end--;
            }
            line[end] = '\0'; // Remplacer le caractère de fin de ligne par '\0' pour couper la chaîne
            
        } else {
            finForme = true;
            end = utf8_strlen(line);
        }
        
        strcat(text, "| ");
        strcat(text, line);

        for (int i = 0; i < line_length - utf8_strlen(line); i++)
        {
            
            strcat(text, " ");
        }
        
        strcat(text, " |\n");

        start += strlen(line)+1;
        
        
    }
    
    strcat(text, "+----------------------------------------------------------------+\n");

    text[strlen(text)] = '\0';

    strcpy(input, text);
}

int utf8_strlen(char *str) {
    int count = 0;
    while (*str) {
        // Vérifie si l'octet n'est pas un octet de continuation (0x80 - 0xBF)
        if ((*str & 0xC0) != 0x80) {
            count++; // Nouveau caractère trouvé
        }
        str++;
    }
    return count;
}

// Fonction pour copier jusqu'à n caractères visibles en UTF-8
void utf8_strncpy(char *dest, const char *src, size_t n) {
    size_t i = 0, j = 0;
    size_t char_count = 0;

    // Parcourir le texte source
    while (src[i] != '\0' && char_count < n) {
        // Si l'octet actuel n'est pas un octet de continuation (0x80 - 0xBF), c'est un début de caractère
        if ((src[i] & 0xC0) != 0x80) {
            char_count++;  // Nouveau caractère visible trouvé
        }

        // Copier l'octet courant dans la destination
        dest[j] = src[i];
        i++;
        j++;
    }

    // Ajouter la terminaison null
    dest[j] = '\0';
}

void changer_format_date(char *date) {
    char temp[11];

    // Extraire le jour, le mois et l'année de la date d'entrée
    int annee, mois, jour;
    sscanf(date, "%4d-%2d-%2d", &annee, &mois, &jour);

    // Formater la nouvelle date sous le format DD/MM/YYYY
    snprintf(temp, sizeof(temp), "%02d/%02d/%04d", jour, mois, annee);

    // Copier la date formatée dans l'entrée originale
    strcpy(date, temp);
}

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
    char respToClient[256];

    int id = -1;

    bool quitter = false;

    while ((*compte == 0) && (quitter == false)) {
        
        memset(buff, 0, sizeof(buff));
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
            snprintf(respToClient, sizeof(respToClient),  // envoie code 200
                "{\"reponse\":\"%d\","
                "\"compte\":\"1\","
                "\"id\":\"%d\"}", OK, id);
        } else if (PQntuples(res2) > 0) {
            *compte = 2; // Utilisateur professionnel (public)
            id = atoi(PQgetvalue(res2, 0, PQfnumber(res, "id_c")));
            snprintf(respToClient, sizeof(respToClient),  // envoie code 200
                "{\"reponse\":\"%d\","
                "\"compte\":\"2\","
                "\"id\":\"%d\"}", OK, id);
        } else if (PQntuples(res3) > 0) {
            *compte = 2; // Utilisateur professionnel (privee)
            id = atoi(PQgetvalue(res3, 0, PQfnumber(res, "id_c")));
            snprintf(respToClient, sizeof(respToClient),  // envoie code 200
                "{\"reponse\":\"%d\","
                "\"compte\":\"2\","
                "\"id\":\"%d\"}", OK, id);
        } else if (strcmp(buff, config.cle_api_admin) == 0) { // Se connecter en tant qu'administrateur
            *compte = 3; // Utilisateur administrateur
            snprintf(respToClient, sizeof(respToClient),  // envoie code 200
                "{\"reponse\":\"%d\","
                "\"compte\":\"2\","
                "\"id\":\"%d\"}", OK, id);
        } else if (strcmp(buff, "-1") == 0) { // Se déconnecter
            quitter = true;
            snprintf(respToClient, sizeof(respToClient), "{\"reponse\":\"402\"}");  // envoie de 402
        } else {  // Clé API incorrecte
            snprintf(respToClient, sizeof(respToClient), "{\"reponse\":\"401\"}");  // envoie de 401
        }
        respToClient[strlen(respToClient)] = '\0';
        write(cnx, respToClient, strlen(respToClient));
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
    char liste_rs[2048] = {0};
    char liste_index[2048] = {0};


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
    int id_c_col = PQfnumber(res, "id_c");
    if (rais_soc_col == -1) {
        fprintf(stderr, "Les colonnes 'id' ou 'nom' sont introuvables dans le résultat\n");
        
    }

    rows = PQntuples(res);

    // Formater chaque ligne avec snprintf
    char ligne[128]; // Tampon pour une ligne
    snprintf(ligne, sizeof(ligne), "[\"%s\"",PQgetvalue(res, 0, rais_soc_col));
    strcat(liste_rs, ligne);

    snprintf(ligne, sizeof(ligne), "[\"%s\"",PQgetvalue(res, 0, id_c_col));
    strcat(liste_index, ligne);

    for (int i = 1; i < rows; i++)
    {
        // Formater chaque ligne avec snprintf
        char ligne[128]; // Tampon pour une ligne
        snprintf(ligne, sizeof(ligne), ",\"%s\"", PQgetvalue(res, i, rais_soc_col));
        strcat(liste_rs, ligne);

        snprintf(ligne, sizeof(ligne), ",\"%s\"", PQgetvalue(res, i, id_c_col));
        strcat(liste_index, ligne);
        
    }
    strcat(liste_rs, "]");
    strcat(liste_index, "]");


    // Préparation et envoie de la réponse
    char response[512] = "{";
    strcat(response,"\"state\":\"200\"");
    strcat(response,",\"data\":");strcat(response,liste_rs);
    strcat(response,",\"indexs\":");strcat(response,liste_index);
    strcat(response,"}");

    write(cnx, response, strlen(response));
}

void send_mess(int cnx, ConfigSocketMessages config, PGconn *conn, int id, char* requete){
    printf("content : %s\n", requete);

    PGresult *res;
    char query[512]; // Buffer statique de taille fixe pour la requête

    // Construire la requête avec snprintf
    snprintf(query, sizeof(query),
        "insert into tripskell._message (contentMessage, idReceveur, idEnvoyeur) values "
        "('%s', %s, %d);", get_json_value(requete, "message"),get_json_value(requete, "receiver"), id);

    

    // Exécuter la requête
    res = PQexec(conn, query);

    // Vérifier si la requête a réussi
    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        fprintf(stderr, "Query execution failed: %s\n", PQerrorMessage(conn));
        PQclear(res);
    }

    write(cnx, "{\"reponse\":\"200\"}", utf8_strlen("{\"reponse\":\"200\"}"));
}

int count_json_array_elements(const char* json_array) {
    if (json_array == NULL || json_array[0] != '[' || json_array[strlen(json_array) - 1] != ']') {
        return -1; // Format invalide
    }

    int count = 0;
    const char* ptr = json_array + 1; // Ignore le '['

    while (*ptr != ']') {
        if (*ptr == '"') { // Début d'un élément de type chaîne
            ptr++;
            while (*ptr != '"' && *ptr != '\0') {
                ptr++; // Ignore le contenu de la chaîne
            }
            if (*ptr == '"') {
                count++;
                ptr++;
            }
        } else if (*ptr == ',') {
            ptr++;
        } else {
            ptr++; // Ignore les espaces ou autres caractères
        }
    }

    return count;
}

char* get_json_array_element(const char* json_array, int index) {
    if (json_array == NULL || json_array[0] != '[' || json_array[strlen(json_array) - 1] != ']') {
        return NULL; // Format invalide
    }

    int current_index = 0;
    const char* ptr = json_array + 1; // Ignore le '['

    while (*ptr != ']') {
        if (*ptr == '"') { // Début d'un élément de type chaîne
            ptr++;
            const char* start = ptr;
            while (*ptr != '"' && *ptr != '\0') {
                ptr++; // Ignore le contenu de la chaîne
            }
            if (*ptr == '"') {
                if (current_index == index) {
                    size_t length = ptr - start;
                    char* element = (char*)malloc(length + 1); // +1 pour le caractère nul
                    strncpy(element, start, length);
                    element[length] = '\0';
                    return element;
                }
                current_index++;
                ptr++;
            }
        } else if (*ptr == ',') {
            ptr++;
        } else {
            ptr++; // Ignore les espaces ou autres caractères
        }
    }

    return NULL; // Index non trouvé
}

void request(int sock, char* request, char* response) {
    write(sock, request, utf8_strlen(request));
    read(sock, response, 512);
}

void menu_envoie_message(int sock, int id_c_pro) {
    char mess[512] = {0};
    char req[2048] = {0};
    char buf[512] = {0};
    system("clear");
    printf("Envoie message : \n\n");

    printf(" > ");
    scanf("%s", mess);

    char id_c_pro_char[3] = {0};
    sprintf(id_c_pro_char, "%d", id_c_pro);

    strcpy(req,"{\"requete\":\"send_mess\",");
    strcat(req," \"message\":\"");strcat(req,mess);strcat(req,"\",");
    strcat(req," \"receiver\":\"");strcat(req,id_c_pro_char);strcat(req,"\"");
    strcat(req,"}");

    request(sock,req, buf);
    printf("Reponse : %s\n", buf);
}

void af_menu_liste_pro(int sock) {
    char buf[512] = {0};
    char data_array[512] = {0};
    char index_array[512] = {0};

    write(sock,"{\"requete\":\"liste_pro\"}", strlen("{\"requete\":\"liste_pro\"}"));
    read(sock, buf, 512); 

    strcpy(data_array, get_json_value(buf, "data"));
    strcpy(index_array, get_json_value(buf, "indexs"));

    int nb_item = count_json_array_elements(data_array);

    system("clear");
    printf( "+-------------------------------------+\n"
            "|          Tchatator Membre           |\n"
            "|                                     |\n"
            "| Liste des professionnels            |\n"
            "+-------------------------------------+\n");
    for (int i = 0; i < nb_item; i++) {
        printf("| %s - %s", get_json_array_element(index_array, i), get_json_array_element(data_array, i));
        for (int j = 0; j < 32 - strlen(get_json_array_element(data_array, i)); j++) {
            printf(" ");
        }
        printf("|\n");
    }
    printf( "| [-1] Retour                         |\n"
            "+-------------------------------------+\n");
}

void menu_liste_pro(int sock) {
    int reponse;

    af_menu_liste_pro(sock);

    bool quitter = false;
    
    while (!quitter) {
    
        printf("> Entrez votre choix : ");
        scanf("%d",&reponse);

        switch (reponse) {
            case -1:
                quitter = true;
                break;
            default:
                menu_envoie_message(sock, reponse);
                break;
        }
    }
}

void af_menu_principal(int type_compte) {
    system("clear");
    if (type_compte == MEMBRE) { // Utilisateur membre
        printf("+-------------------------------------+\n"
                    "|          Tchatator Membre           |\n"
                    "+-------------------------------------+\n"
                    "| [1] Voir les messages non lus       |\n"
                    "| [2] Voir ma conversation avec       |\n"
                    "|     un professionnel                |\n"
                    "| [3] Contacter un nouveau            |\n"
                    "|     professionnel                   |\n"
                    "| [-1] Quitter                        |\n"
                    "+-------------------------------------+\n");

    } else if (type_compte == PRO) { // Utilisateur professionnel
        printf("+-------------------------------------+\n"
                    "|       Tchatator professionnel       |\n"
                    "+-------------------------------------+\n"
                    "| [1] Voir les messages non lus       |\n"
                    "| [2] Voir ma conversation avec       |\n"
                    "|     un membre                       |\n"
                    "| [-1] Quitter                        |\n"
                    "+-------------------------------------+\n");

    } else if (type_compte == ADMIN) { // Utilisateur administrateur
        printf("+-------------------------------------+\n"
                    "|      Tchatator Administrateur       |\n"
                    "+-------------------------------------+\n"
                    "| [1] Bloquer un utilisateur          |\n"
                    "| [2] Bannir un utilisateur           |\n"
                    "| [-1] Quitter                        |\n"
                    "+-------------------------------------+\n");
    } else {
        printf("erreur");
    }
    
}

int menu_principal(int cnx, int compte, int id, int sock) {
    char buff[512];
    int id_c = -1;
    int ret;
    int reponse;
    int rep = -1;
    
    bool quitter = false;
    
    while (quitter == false) {
        af_menu_principal(compte);
    
        printf("> Entrez votre choix : ");
        scanf("%d",&reponse);

        // Liste des differents choix selon le compte :
        // Pour un membre
        if (compte == MEMBRE) {

            switch (reponse) {
                case 1:  // Si il choisit de voir ses messages non lus (membre)
                    /* TODO voir les messages non lus (membre) */
                    break;
                
                case 2:  // Si il choisit de voir une conversation déjà entamée (membre)
                    /* TODO voir ma conversation avec un pro (membre) */
                    break;

                case 3:  // Si il choisit d'envoyer un message

                    menu_liste_pro(sock);
                    
                    break;

                case -1:  // Se déconnecter
                    write(sock,"{\"requete\":\"deconnexion\"}", strlen("{\"requete\":\"deconnexion\"}"));
                    
                    int len = read(sock, buff, 17);
                    
                    if (len < 0) {
                        perror("Erreur lors de la lecture");
                        return -1;
                    }

                    if (atoi(get_json_value(buff, "reponse")) == DECO) {
                        quitter = true;
                        printf("Deconnexion ...");
                    }
                    
                    break;
                
                default:  // Choix non valide
                    break;
            }

            memset(buff, 0, sizeof(buff));
        }

        // Pour un professionnel
        else if (compte == PRO) {

            switch (reponse) {
                case 1:  // Si il choisit de voir ses messages non lus (pro)
                    /* TODO voir les messages non lus (pro) */
                    break;
                
                case 2:  // Si il choisit de voir une conversation déjà entamée (pro)
                    /* TODO voir ma conversation avec un membre (pro) */
                    break;

                case -1:  // Se déconnecter
                    write(sock,"{\"requete\":\"deconnexion\"}", strlen("{\"requete\":\"deconnexion\"}"));
                    
                    int len = read(sock, buff, 17);
                    
                    if (len < 0) {
                        perror("Erreur lors de la lecture");
                        return -1;
                    }

                    if (atoi(get_json_value(buff, "reponse")) == DECO) {
                        quitter = true;
                        printf("Deconnexion ...");
                    }
                    
                    break;
                
                default:  // Choix non valide
                    break;
            }

        }
        
        // Pour un administrateur
        else if (compte == ADMIN) {

            switch (reponse) {
                case 1:  // Si il choisit de bloquer un utilisateur (admin)
                    /* TODO bloquer un utilisateur (admin) */
                    break;

                case 2:  // Si il choisit de bannir un utilisateur (admin)
                    /* TODO bannir un utilisateur (admin) */
                    break;

                case -1:  // Se déconnecter
                    write(sock,"{\"requete\":\"deconnexion\"}", strlen("{\"requete\":\"deconnexion\"}"));
                    
                    int len = read(sock, buff, 17);
                    
                    if (len < 0) {
                        perror("Erreur lors de la lecture");
                        return -1;
                    }

                    if (atoi(get_json_value(buff, "reponse")) == DECO) {
                        quitter = true;
                        printf("Deconnexion ...");
                    }
                    
                    break;
                
                default:  // Choix non valide
                    break;
            }

        } else {
            perror("Utilisateur non connecté sur le menu principal");
            
            return -1; // Utilisateur non connecté
        }
    }

    return rep;
}

int lirePort(const char *filename, int *numPort) {
    FILE *file;
    char line[256];
    bool trouve = false;

    // Ouvrir le fichier
    file = fopen(filename, "r");
    if (file == NULL) {
        fprintf(stderr, "Impossible d'ouvrir le fichier %s\n", filename);
        return -1;
    }


    // Lire chaque ligne du fichier
    while ((fgets(line, sizeof(line), file)) && (!trouve)) {
        // Supprimer le caractère de fin de ligne si présent
        line[strcspn(line, "\n")] = 0;

        // Vérifier si la ligne commence par "port=" et lire la valeur
        if (strncmp(line, "port=", strlen("port=")) == 0) {
            // Lire l'entier après "port="
            if (sscanf(line + strlen("port="), "%d", numPort) == 1) {
                trouve = true;
            }
        }
    }


    if (trouve != true) {
        return -1;
    }
    

    return 0;
}
