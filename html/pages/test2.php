<br />
<b>Fatal error</b>:  Uncaught PDOException: SQLSTATE[42703]: Undefined column: 7 ERROR:  column &quot;horaire_debut&quot; of relation &quot;_horaire&quot; does not exist
LINE 1: INSERT INTO tripskell._horaire (horaire_debut, horaire_fin) ...
                                        ^
QUERY:  INSERT INTO tripskell._horaire (horaire_debut, horaire_fin) VALUES (deb_matin::TIME, fin_matin::TIME) returning id_hor
CONTEXT:  PL/pgSQL function tripskell.add_horaire(integer,character varying,character varying,character varying,character varying,character varying) line 5 at SQL statement in C:\Users\bapti\OneDrive\Documents\SAE_PACT\PACT\html\php\changeHoraireOffre.php:34
Stack trace:
#0 C:\Users\bapti\OneDrive\Documents\SAE_PACT\PACT\html\php\changeHoraireOffre.php(34): PDOStatement-&gt;execute()
#1 {main}
  thrown in <b>C:\Users\bapti\OneDrive\Documents\SAE_PACT\PACT\html\php\changeHoraireOffre.php</b> on line <b>34</b><br />