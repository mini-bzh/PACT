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
    char server_reply[1024];
    struct sockaddr_in addr;

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

    if (atoi(server_reply) != OK){
        printf("Erreur ou déconnexion lors de la réception du message initial.\n");
        close(sock);
        return -1;
    }
    
    ret = menu_connexion(sock, &type_compte);
    if (ret == -1)
    {
        printf("Deconnexion ...\n");
        close(sock);
        return 0;
    }

    sprintf(server_reply, "%d", ret);
    
    /*while (atoi(server_reply) != DECO){
        system("clear");
        af_menu_principal(type_compte);*/
    menu_principal(cnx, type_compte, -1, sock);
        // sprintf(server_reply, "%d", rep);
    //}

    
    // Fermer la socket
    close(sock);

    return 0;
}
