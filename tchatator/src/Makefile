# Variables
CC = gcc
CFLAGS = -Wall
LDFLAGS = -lpq
SRC_SERVER = server.c fonctions.c constantes.c
SRC_CLIENT = client.c fonctions.c menus.c constantes.c
OBJ_DIR = ../build
OBJ_SERVER = $(SRC_SERVER:%.c=$(OBJ_DIR)/%.o)
OBJ_CLIENT = $(SRC_CLIENT:%.c=$(OBJ_DIR)/%.o)
EXEC_DIR = ../bin
EXEC_SERVER = $(EXEC_DIR)/server
EXEC_CLIENT = $(EXEC_DIR)/client

# Créer les répertoires nécessaires
$(shell mkdir -p $(OBJ_DIR) $(EXEC_DIR))

# Règle par défaut : crée les deux exécutables
all: $(EXEC_SERVER) $(EXEC_CLIENT)

# Compilation du premier exécutable
$(EXEC_SERVER): $(OBJ_SERVER)
	$(CC) $(OBJ_SERVER) -o $(EXEC_SERVER) $(LDFLAGS)

# Compilation du deuxième exécutable
$(EXEC_CLIENT): $(OBJ_CLIENT)
	$(CC) $(OBJ_CLIENT) -o $(EXEC_CLIENT) $(LDFLAGS)

# Compilation des fichiers objets (.o) à partir des fichiers source (.c)
$(OBJ_DIR)/%.o: %.c
	$(CC) $(CFLAGS) -c $< -o $@

# Nettoyage des fichiers objets et des exécutables
clean:
	rm -f $(OBJ_SERVER) $(OBJ_CLIENT) $(EXEC_SERVER) $(EXEC_CLIENT)

# Nettoyage complet, y compris les fichiers temporaires
fclean: clean
	rm -f $(EXEC_SERVER) $(EXEC_CLIENT)

# Recompiler tout à partir de zéro
re: fclean all

# Affichage des fichiers objets
objs:
	@echo $(OBJ_SERVER) $(OBJ_CLIENT)