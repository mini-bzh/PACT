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


#include "types.h"
#include "prototypes.h"


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
        write(cnx, "1", 1);
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

// Fonction pour gérer le menu principal
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
        write(cnx, "1", 1);
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
                    
                    if (id_c != -1) {
                        ret = menu_conversation_membre(cnx, id_c, id, conn);
                        if (ret == -1) {
                            return -1;
                        }
                    }
                    
                    break;

                case -1:  // Se déconnecter
                    quitter = true;
                    
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
                    
                    break;
                
                default:  // Choix non valide
                    strcpy(affichage, menu);
                    strcat(affichage, "Choix non valide, réessayez : ");
                    break;
            }

        } else {
            perror("Utilisateur non connecté sur le menu principal");
            
            return -1; // Utilisateur non connecté
        }
    }

    return 0;
}

// Fonction pour gérer le menu affichant la liste des professionnel pas encore contactés (membre)
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
        
        write(cnx, "1", 1);
        write(cnx, liste, strlen(liste));
        
        int len = read(cnx, buff, sizeof(buff) - 1);
        if (len < 0) {
            perror("Erreur lors de la lecture");
            return -1;
        }

        buff[len] = '\0';

        if (atoi(buff) == -1) { // Retour au menu principal
            quitter = true;
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

// Fonction pour gérer le menu de conversation avec un professionnel (membre)
int menu_conversation_membre(int cnx, int id_c, int id, PGconn *conn) {
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
        write(cnx, "1", 1);
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

// Fonction pour gérer le menu d'envoie de messages
int menu_envoyerMessage(int cnx, bool compte, int id_c, int id, PGconn *conn) {
    // TODO
    return -1;
}
