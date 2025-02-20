#ifndef PROTOTYPES_H
#define PROTOTYPES_H

#include <libpq-fe.h>
#include <ctype.h>

#include "types.h"

////////////////////////////////////
//                                //
//   PROTOTYPES DES FONCTIONS     //
//                                //
////////////////////////////////////

int lire_config(const char *filename, ConfigSocketMessages *configSocket, ConfigBDD *configBDD);  // Lit la configuration du server

PGconn *connect_to_db(ConfigBDD *configBDD);  // Connexion à la BDD

int create_socket(ConfigSocketMessages *configSocket);  // Crée un socket

void afficher_tout(PGresult *res);  // Fonction de debug pour afficher le resultat d'une requête

int afficher_message(char *nom, char *date, char *modif, char *mess, int num);  // Fonction pour afficher un message

void wrap_text(char *input, int line_length);  // Fonction qui permet de formater un message

int utf8_strlen(char *str);  // Fonction qui compte le nombre de caractères d'un mot (strlen renvoie le nombre d'octets)

void utf8_strncpy(char *dest, const char *src, size_t n);  // Fonction qui copie n caractères dans dest à partir de src (strncpy le fait en octets)

void changer_format_date(char *date); // Changer le format de la date (de 2024-12-01 à 01/12/2024 par exemple)

char* get_json_value(char* json, char* key);  // Fonction pour extraire la valeur associée à une clé dans un JSON

int count_json_array_elements(char* json_array);

char* get_json_array_element(char* json_array, int index);

int identification(int cnx, ConfigSocketMessages config, int *compte, PGconn *conn);

void reponse_liste_pro(int cnx, ConfigSocketMessages config, PGconn *conn, int id, char* requete);

void reponse_liste_membre(int cnx, ConfigSocketMessages config, PGconn *conn, int id);

void send_mess(int cnx, ConfigSocketMessages config, PGconn *conn, int id, char* requete);

void historique_mess(int cnx, ConfigSocketMessages config, PGconn *conn, int id, char* requete);

void menu_historique_messages(int sock, int id_c);

int lirePort(const char *filename, int *numPort);

int envoie_mess_non_lu(int cnx, int id, PGconn *conn);


////////////////////////////////////
//                                //
//     PROTOTYPES DES MENUS       //
//                                //
////////////////////////////////////

int menu_connexion(int cnx, int *compte);  // Menu de connexion

// int menu_principal(int cnx, int compte, int id, PGconn *conn);  // Menu principal (choix des actions possibles)

int menu_listePro(int cnx, int id, PGconn *conn);  // menu de la liste des pros à contacter (renvoie l'id du pro à contacter)

int menu_conversation_membre(int cnx, int id_c, int id, PGconn *conn);

int menu_envoyerMessage(int cnx, bool compte, int id_c, int id, PGconn *conn);

void af_menu_liste_pro(int sock, bool nouv);

void menu_liste_pro(int sock, bool nouv);

void af_menu_principal(int type_compte);

int menu_principal(int cnx, int compte, int id, int sock);

int menu_nouv_mess(int sock); // menu des messages non lus

int menu_modif_message(int sock, int id_c);

int menu_sup_message(int sock, int id_c);

void aff_modif_sup_messages(int sock, int id_c);

int modif_mess(int cnx, PGconn *conn, int id_c, int mon_id, int id_mess);

int sup_mess(int cnx, PGconn *conn, int id_c, int mon_id, int id_mess);

#endif // PROTOTYPES_H
