#include <stdbool.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <stdio.h>
#include <string.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h>

#include "types.h"
#include "prototypes.h"

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

    ret = menu_connexion(sock, &type_compte);
    if (ret == -1)
    {
        printf("Le Server a fermé la connexion\n");
        close(sock);
        return 0;
    }
    

    // Envoyer le message au serveur
    // if (write(sock, message, strlen(message)) < 0) {
    //     perror("Erreur lors de l'envoi du message");
    // }

    // read(sock, server_reply, 3);

    // while (atoi(server_reply) == 401){
    //     system("clear");
    //     printf("MENU CONNECTION ( Cle incorrect )\n\n");
    //     printf("cle API : ");

    //     fgets(message, sizeof(message), stdin);
    //     message[strcspn(message, "\n")] = '\0'; // Supprimer le '\n' de fgets

    //     // Envoyer le message au serveur
    //     if (write(sock, message, strlen(message)) < 0) {
    //         perror("Erreur lors de l'envoi du message");
    //         break;
    //     }

    //     read(sock, server_reply, 3);
    // }

    // read(sock, server_reply, 1);

    // type_compte = (int)server_reply[0] - '0';

    // af_menu_principal(type_compte);
    // menu_principale(cnx, type_compte, -1, sock);
    
    // Fermer la socket
    close(sock);

    return 0;
}
