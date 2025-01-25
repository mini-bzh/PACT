#ifndef PROTOTYPES_H
#define PROTOTYPES_H

#include "types.h"
#include <libpq-fe.h>


////////////////////////////////////
//                                //
//   PROTOTYPES DES FONCTIONS     //
//                                //
////////////////////////////////////

int lire_config(const char *filename, ConfigSocketMessages *configSocket, ConfigBDD *configBDD);  // Lit la configuration du server

PGconn *connect_to_db(ConfigBDD *configBDD);  // Connexion à la BDD

int create_socket(ConfigSocketMessages *configSocket);  // Crée un socket

void afficher_tout(PGresult *res);  // Fonction de debug pour afficher le resultat d'une requête


////////////////////////////////////
//                                //
//     PROTOTYPES DES MENUS       //
//                                //
////////////////////////////////////

int menu_connexion(int cnx, ConfigSocketMessages config, int *compte, PGconn *conn);  // Menu de connexion
int menu_principal(int cnx, int compte, int id, PGconn *conn);  // Menu principal (choix des actions possibles)

int menu_listePro(int cnx, int id, PGconn *conn);  // menu de la liste des pros à contacter (renvoie l'id du pro à contacter)

int menu_conversation_membre(int cnx, int id_c, int id, PGconn *conn);

int menu_envoyerMessage(int cnx, bool compte, int id_c, int id, PGconn *conn);

#endif // PROTOTYPES_H