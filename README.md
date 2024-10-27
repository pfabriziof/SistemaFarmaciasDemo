# SistemaFarmaciasDemo
El siguiente repositorio contiene una versión DEMO del sistema integrado de "Gestión de Farmacias" para la tesis hecha sobre esta plataforma. Puede visualizar el alcance contemplado para este proyecto en el siguiente diagrama.

Live Demo: https://farmacias.morrisopazo-datascience.com/login

<img src="https://github.com/paolo-fabrizio/SistemaFarmaciasDemo/blob/main/public/assets/images/demo_version_alcance.png" alt="Alcance Demo" align="center">

# Arquitectura de la Integración con IA Generativa
El siguiente diagrama representa la arquitectura diseñada para la integración entre nuestra aplicación web y el modelo IA generativo. Podemos observar que la pregunta del usuario se transforma en una consulta SQL gracias a un primer llamado al LLM, luego esta consulta es ejecutada sobre la base de datos y, finalmente, se utiliza nuevamente la comunicación con el LLM para retornar una respuesta en lenguaje natural tomando en cuenta los resultados retornados por la base de datos.  

<img src="https://github.com/paolo-fabrizio/SistemaFarmaciasDemo/blob/main/public/assets/images/arquitectura_integracion_iagenerativa.png" alt="Arquitectura Integración con IA Generativa" align="center">

# Documentacion
Puede consultar el informe de tesis y demás documentos en los links a continuación.  
- [Informe de tesis]()
- [Diagrama relacional de base de datos](https://www.dropbox.com/scl/fi/tt6t58m5hi46vql3lcxz6/DatabaseDiagram.png?rlkey=m2dgv5y67hcgxb8afvpm63hm7&dl=0)
- [Planificación del proyecto](https://www.dropbox.com/scl/fi/laxl9y1ibua8jfntda3fy/Gantt_Cronograma.png?rlkey=lb0f7v5k7ultvifojg2a8xy8p&dl=0)
- [BPMN proceso de venta](https://www.dropbox.com/scl/fi/odnq5dfvpjvcj73yjnq5q/BPMN_ProcesoVenta.jpg?rlkey=d7zmiyqq4tajw2tsu667zxk82&e=1&dl=0)
