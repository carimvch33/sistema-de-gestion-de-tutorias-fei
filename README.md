# TutoriasFEI

## Descripción
El proyecto **TutoriasFEI** tiene como objetivo registrar las sesiones de tutoría, gestionar los reportes generados y dar seguimiento a las problemáticas reportadas por los estudiantes.

## Tecnología Utilizada
- **PHP puro**

## Estado del Proyecto
Este proyecto fue recibido en **enero de 2025** e incluía el código fuente y diagramas. Con las horas disponibles, se realizaron algunos cambios en funcionalidades y en la arquitectura del sistema. Sin embargo, aún quedan pendientes varias mejoras, entre ellas:
- Mejoras en la calidad del código.
- Optimización de los archivos CSS.
- Inclusión de un archivo **.env** para la configuración del entorno.
- Mejora en los diagramas de diseño.
- Verificación completa de la funcionalidad del sistema.

## Base de Datos
La base de datos del proyecto se encuentra en el archivo **db.sql**.

## Diagramas
Los diagramas proporcionados se encuentran en la carpeta **diagrams**.

## Archivos de Datos
En la carpeta **data**, se incluyen:
- **insertTutoriados.xlsx**: Contiene información sobre los estudiantes tutorados. Aún falta agregar algunos datos, lo cual ya se comentó con la Mtra. Ericka.
- Archivos de Excel proporcionados originalmente, los cuales serán unificados en un solo documento con la información completa de los estudiantes.

## Despliegue del Proyecto
- Al momento de realizar el despliegue, es importante revisar el archivo **config/config.php** y modificar la variable **ENVIRONMENT** según sea necesario. Se recomienda migrar esta configuración a un archivo **.env** para una mejor gestión del entorno.
- Se recomienda practicar el despliegue en un entorno local antes de realizarlo en un entorno de producción, ya que de lo contrario suelen surgir varios problemas técnicos durante el proceso.

## Control de versiones

Todos los nombrados descritos en esta sección, a pesar de estar explicados en español, deberán hacerse
en inglés en la práctica.

### Nombrado de commits

Cada commit deberá estar nombrado siguiendo la siguiente especificación:

```tag(archivo o módulo afectado): descripción extendida de lo que realiza el commit```

El `tag` es una etiqueta estandar definida por los colaboradores:

* `feat`: indica que el commit está incluyendo un `feature` o funcionalidad nueva.
* `config`: indica que el commit está cambiando algún archivo de configuración, instaló alguna biblioteca 
para el proyecto o cambió la estructura de organización de algún módulo o carpeta.
* `fix`: indica que el commit está realizando un cambio sobre alguna funcionalidad existente, ya sea para resolver
un bug, cambiar la lógica o mejorar el rendimiento.
* `docs`: indica que el commit está realizando un cambio en documentos del repositorio fuera del código, como 
el archivo md README que se encuentra leyendo.
* `revert`: reversión a un commit anterior.
* `test`: indica que el commit agrega, corrige o elimina una prueba unitaria del proyecto

Tome en cuenta que la _descripción extendida de lo que realiza el commit_ debe estar escrita desde el punto de vista
de este. Ejemplo:

`feat(app.ts): implements call to AuthRouter and exposes its services`

Note cómo se usan las palabras "implements" y "exposes" para indicar que el commit está realizando o implementando
esto. Un mal nombrado de un commit sería violando esta regla. Ejemplo:

`feat(app.ts): implementation of AuthRouter exposition of services`

### Nombrado de pull requests (PR)

Cada PR deberá estar nombrado siguiendo la siguiente especificación:

```TAG: nombre representativo de lo que se incluye```

El `tag` cumple una función parecida al `tag` de un commit, pues indica el tipo de PR que se realiza, sin embargo, en este
caso hay cinco posibles opciones: `FEAT`, `FIX` `CONFIG`, `DOCS` y `TEST`. Se espera que habiendo explicado sus significados
para un commit, el lector pueda intuir sus significados en un PR.

En este caso, el _nombre representativo de lo que se incuye_ en el PR, **no** debe estar escrito como en el commit, es decir, 
"desde su punto de vista". En este caso, sí debe colocarse de la forma:

```FEAT: implementation of login endpoint```

En lugar de:

```FEAT: implements login endpoint```

Tome en cuenta que un PR es un "hito", trate de describir ese hito. 

### Nombrado de ramas

Se recomienda que, para llevar una mejor organización de las ramas, estas se nombren de la siguiente forma:

```referencia-caso-uso/tag/descripcion```

Por ejemplo, si el caso de uso es "Evaluar artículo para subastar" y se desea implementar un `feature` que englobe la 
construcción de un endpoint asociado, un nombre recomendable sería:

```auction-evaluation/feat/endpoint-descriptive-name```

Mientras que un nombre no recomendado sería:

```endpoint_name```