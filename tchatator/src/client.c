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
    read_size = read(sock, server_reply, sizeof(server_reply) - 1);
    if (read_size > 0) {
        server_reply[read_size] = '\0';
        printf("%s", server_reply);
    } else {
        printf("Erreur ou déconnexion lors de la réception du message initial.\n");
        close(sock);
        return -1;
    }

    while (quitter == false) {
        fgets(message, sizeof(message), stdin);
        message[strcspn(message, "\n")] = '\0'; // Supprimer le '\n' de fgets

        // Envoyer le message au serveur
        if (write(sock, message, strlen(message)) < 0) {
            perror("Erreur lors de l'envoi du message");
            break;
        }

        // Lire la réponse du serveur
        read_size = read(sock, server_reply, sizeof(server_reply) - 1);
        if (read_size <= 0) {
            printf("Le serveur a fermé la connexion.\n");
            quitter = true;
            return 0;
        }
        server_reply[read_size] = '\0';
        printf("%s", server_reply);
    }
    
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