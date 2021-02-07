# m2-developertoolbar : documentation

## BO
La toolbar peut etre activé sur le front depuis la confid "Stores > Configuration > Advanced > Developer > Developer Toolbar > Enabled in FO"

## FO
![](images/dt.png)

### les differentes requetes

La toolbar attrape chaque requete entrante dans Magento.

Si la requete est affichable alors elle l'est, sinon elle est stockée en session et sera affichée la prochaine fois que la toolbar le sera.

Une requete est considéré comme non affichage si :
- c'est une requete AJAX
- la requete fini en redirection

### Show / Hide

le chevron a gauche de toolbar permet de l'afficher / masquer

### Les onglets
- Request : Données sur la requête
- Layout : Données sur le layout
- Events : Les Events / Observers
- Models : Les Models / Collections
- Plugins : La liste des plugins
- Database : Les requêtes sur la base de données
- Server : Info sur le serveur
- Profiler : Le profiler Magento
- Memory : Memoire utilisée / Memoire allouée
- Time : Temps de génération (hors Toolbar)

### Request

#### Request
![](images/dt_request_request.png)

#### Var GET / POST / COOKIE
![](images/dt_request_var.png)

### Layout

#### Tree Blocks
![](images/dt_layout_tree.png)

#### Blocks

Les blocs triés par odre de temps de génération

![](images/dt_layout_blocks.png)

#### Handles
![](images/dt_layout_handles.png)

#### Xml
![](images/dt_layout_xml.png)

### Events

#### Events/Observers

Relation entre Event et Observer

![](images/dt_event_events.png)

#### Observers

Les observers triés par ordre de temps d'execution

![](images/dt_event_observers.png)

### Models

#### Models

![](images/dt_model_models.png)

#### Collections

![](images/dt_model_collections.png)


### Plugins

![](images/dt_plugins.png)

### Database

#### Queries

![](images/dt_database_queries.png)

#### Multiple Queries

![](images/dt_database_multiple_queries.png)

### Server

Le PhpInfo du server

### Profiler
![](images/dt_profiler.png)
