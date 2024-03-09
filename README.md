# SistemaFarmaciasDemo
El siguiente repositorio contiene una versión DEMO del sistema integrado de "Gestión de Farmacias" para la tesis hecha sobre esta plataforma. Puede visualizar el alcance contemplado para este proyecto en el siguiente diagrama.

<img src="https://github.com/paolo-fabrizio/SistemaFarmaciasDemo/blob/main/public/assets/images/demo_version_alcance.png" alt="Alcance Demo" align="center">

# Arquitectura de la Integración con Chat GPT
El siguiente diagrama representa la arquitectura diseñada para la integración entre nuestra aplicación web y ChatGPT. Podemos observar que la pregunta del usuario se transforma en una consulta SQL gracias a un primer llamado al LLM, luego esta consulta es ejecutada sobre la base de datos y, finalmente, se utiliza nuevamente la comunicación con el LLM para retornar una respuesta en lenguaje natural tomando en cuenta los resultados retornados por la base de datos.  

<img src="https://github.com/paolo-fabrizio/SistemaFarmaciasDemo/blob/main/public/assets/images/arquitectura_integracion_chatgpt.jpg" alt="Arquitectura Integración con Chat GPT" align="center">

# Documentacion
Puede consultar el informe de tesis y demás documentos en los links a continuación.  
[LINKS]
