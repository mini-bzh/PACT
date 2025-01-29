#include <stdbool.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <stdio.h>
#include <string.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h>

// Prototypes des fonctions
int lirePort(const char *filename, int *numPort);

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

void af_menu_liste_pro(int sock) {
    char buf[512+1] = {0};
    char data_array[512+1] = {0};

    write(sock,"{\"requete\":\"liste_pro\"}", strlen("{\"requete\":\"liste_pro\"}"));
    read(sock, buf, 512); 

    strcpy(data_array, get_json_value(buf, "data"));

    int nb_item = count_json_array_elements(data_array);

    system("clear");
    printf( "+-------------------------------------+\n"
            "|          Tchatator Membre           |\n"
            "|                                     |\n"
            "| Liste des professionnels            |\n"
            "+-------------------------------------+\n");
    for (int i = 0; i < nb_item; i++) {
        printf("| %d - %s", i+1, get_json_array_element(data_array, i));
        for (int j = 0; j < 32 - strlen(get_json_array_element(data_array, i)); j++) {
            printf(" ");
        }
        printf("|\n");
    }
    printf( "| [-1] Retour                         |\n"
            "+-------------------------------------+\n");
}
void menu_liste_pro(int sock) {
    af_menu_liste_pro(sock);
}

void af_menu_principal(int type_compte) {
    system("clear");
    if (type_compte == 1) { // Utilisateur membre
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

    } else if (type_compte == 2) { // Utilisateur professionnel
        printf("+-------------------------------------+\n"
                    "|       Tchatator professionnel       |\n"
                    "+-------------------------------------+\n"
                    "| [1] Voir les messages non lus       |\n"
                    "| [2] Voir ma conversation avec       |\n"
                    "|     un membre                       |\n"
                    "| [-1] Quitter                        |\n"
                    "+-------------------------------------+\n");

    } else if (type_compte == 3) { // Utilisateur administrateur
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

int menu_principale(int cnx, int compte, int id, int sock) {
    char buff[50];
    int id_c = -1;
    int ret;
    int reponse;
    
    bool quitter = false;
    
    while (quitter == false) {
    
        printf("> Entrez votre choix : ");
        scanf("%d",&reponse);

        // Liste des differents choix selon le compte :
        
        // Pour un membre
        if (compte == 1) {

            switch (reponse) {
                case 1:  // Si il choisit de voir ses messages non lus (membre)
                    /* TODO voir les messages non lus (membre) */
                    break;
                
                case 2:  // Si il choisit de voir une conversation déjà entamée (membre)
                    /* TODO voir ma conversation avec un pro (membre) */
                    break;

                case 3:  // Si il choisit d'envoyer un message
                    /*id_c = -1;
                    id_c = menu_listePro(cnx, id, conn);
                    
                    if (id_c != -1) {
                        ret = menu_conversation_membre(cnx, id_c, id, conn);
                        if (ret == -1) {
                            return -1;
                        }
                    }*/
                   menu_liste_pro(sock);
                    
                    break;

                case -1:  // Se déconnecter
                    quitter = true;
                    
                    break;
                
                default:  // Choix non valide
                    break;
            }

            strcpy(buff, "");
        }

        // Pour un professionnel
        else if (compte == 2) {

            switch (reponse) {
                case 1:  // Si il choisit de voir ses messages non lus (pro)
                    /* TODO voir les messages non lus (pro) */
                    break;
                
                case 2:  // Si il choisit de voir une conversation déjà entamée (pro)
                    /* TODO voir ma conversation avec un membre (pro) */
                    break;

                case -1:  // Se déconnecter
                    quitter = true;
                    
                    break;
                
                default:  // Choix non valide
                    break;
            }

        }
        
        // Pour un administrateur
        else if (reponse) {

            switch (atoi(buff)) {
                case 1:  // Si il choisit de bloquer un utilisateur (admin)
                    /* TODO bloquer un utilisateur (admin) */
                    break;

                case 2:  // Si il choisit de bannir un utilisateur (admin)
                    /* TODO bannir un utilisateur (admin) */
                    break;

                case -1:  // Se déconnecter
                    quitter = true;
                    
                    break;
                
                default:  // Choix non valide
                    break;
            }

        } else {
            perror("Utilisateur non connecté sur le menu principal");
            
            return -1; // Utilisateur non connecté
        }
    }

    return 0;
}

// Fonction principale
int main(int argc, char const *argv[])
{
    int ret;
    int cnx;
    int numPort;
    char message[1024], server_reply[1024];
    struct sockaddr_in addr;
    bool quitter = false;
    int read_size;

    int type_compte;
    
    ret = lirePort("../.config/config.txt", &numPort);
    if (ret != 0) {
        perror("Impossible de lire le port depuis le fichier de configuration");
        return -1;
    }

    int sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock < 0) {
        perror("Erreur lors de la création du socket");
        return -1;
    }

    addr.sin_addr.s_addr = inet_addr("127.0.0.1");
    addr.sin_family = AF_INET;
    addr.sin_port = htons(numPort);

    printf("Connexion au serveur sur le port %d...\n", numPort);
    cnx = connect(sock, (struct sockaddr *)&addr, sizeof(addr));
    if (cnx < 0) {
        perror("Erreur lors de la connexion au serveur");
        return -1;
    }
    printf("Connexion établie avec le serveur\n");

    // Recevoir le message initial
    system("clear");

    read(sock, server_reply, 3);

    if (atoi(server_reply) != 200){
        printf("Erreur ou déconnexion lors de la réception du message initial.\n");
        close(sock);
        return -1;
    }
    //read_size = read(sock, server_reply, sizeof(server_reply) - 1);
    /*if (read_size > 0) {
        server_reply[read_size] = '\0';
        printf("%s", server_reply);
    } else {
        printf("Erreur ou déconnexion lors de la réception du message initial.\n");
        close(sock);
        return -1;
    }*/

    printf("MENU CONNECTION\n\n");
    printf("cle API : ");

    fgets(message, sizeof(message), stdin);
    message[strcspn(message, "\n")] = '\0'; // Supprimer le '\n' de fgets

    // Envoyer le message au serveur
    if (write(sock, message, strlen(message)) < 0) {
        perror("Erreur lors de l'envoi du message");
    }

    read(sock, server_reply, 3);

    while (atoi(server_reply) == 401){
        system("clear");
        printf("MENU CONNECTION ( Cle incorrect )\n\n");
        printf("cle API : ");

        fgets(message, sizeof(message), stdin);
        message[strcspn(message, "\n")] = '\0'; // Supprimer le '\n' de fgets

        // Envoyer le message au serveur
        if (write(sock, message, strlen(message)) < 0) {
            perror("Erreur lors de l'envoi du message");
            break;
        }

        read(sock, server_reply, 3);
    }

    read(sock, server_reply, 1);

    type_compte = (int)server_reply[0] - '0';

    af_menu_principal(type_compte);
    menu_principale(cnx, type_compte, -1, sock);


    /*
    while (!quitter) {
        system("clear");

        

        // Lire la réponse du serveur
        // read_size = read(sock, server_reply, sizeof(server_reply) - 1);
        // if (read_size <= 0) {
        //     printf("Le serveur a fermé la connexion.\n");
        //     quitter = true;
        //     return 0;
        // }
        // server_reply[read_size] = '\0';
        // printf("%s", server_reply);

        /*
        read_size = read(sock, server_reply, 1); // Lire combien de messages sont envoyés
        server_reply[read_size] = '\0';

        if (read_size <= 0) {
            printf("Le serveur a fermé la connexion.\n");
            quitter = true;
            return 0;
        }

        //if (!quitter) {
        
        int message_count = atoi(server_reply);

        for (int i = 0; i < message_count; i++) {
            read_size = read(sock, server_reply, sizeof(server_reply) - 1); // Lire chaque message
            server_reply[read_size] = '\0';
            printf("%s", server_reply);
        }
        //}*/
        /*
        fgets(message, sizeof(message), stdin);
        message[strcspn(message, "\n")] = '\0'; // Supprimer le '\n' de fgets

        // Envoyer le message au serveur
        if (write(sock, message, strlen(message)) < 0) {
            perror("Erreur lors de l'envoi du message");
            break;
        }

    }*/
    
    // Fermer la socket
    close(sock);

    return 0;
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