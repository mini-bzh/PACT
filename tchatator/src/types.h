#ifndef TYPES_H
#define TYPES_H


/////////////////////
//      TYPES      //
/////////////////////

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


// Types de compte
extern const int MEMBRE;
extern const int PRO;
extern const int ADMIN;

// Réponses du serveur
extern const int OK;
extern const int CLEINC;
extern const int DECO;
extern const int ACKDECO;
extern const int FMESSNVU;
extern const int ACKVU;
extern const int MESSNOFO;

#endif // TYPES_H