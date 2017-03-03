#Projeto NFe AENET

##FASE 1

###Objetivo:

Emissão automática de NFe, a partir de TXT gravado em base de dados

###Estrutura:

* S.O. Linux, Debian ou derivado, em sua última atualização LTS
* PHP 7.0 ou superior (temos que estabelecer se usará FPM ou não)
* Banco de Dados MySQL (banco INNODB) (tabelas a serem definidas)

###Pré-Requisitos:

* Acesso ROOT por SSH
* Todos os módulos necessários do PHP intalados e ativos
* PHP Composer
* Certificado digital A1 (via software) para cada empresa usuária

###Funcionamento:

Usando o CRON (agendador) será executado um script periodicamente, em intervalos de tempo ainda a ser definido, mas não menos que 60 segundos.

Esse script (processo) irá:

* buscar em tabela da base de dados, se existe algum TXT ainda não processado e se houver, irá:
  * usar a classe NFePHP\NFe\ConvertNFe() -- erros e exceptions
  * Assinar o XML obtido 
  * Validar o XML assinado -- erros e exceptions
  * Se validado, enviar para a SEFAZ (considerar CONTINGÊNCIA) - erros e exceptions   
  * Carregar tabela da base com os retornos da SEFAZ
  * enviar os email aos destinatários contidos no XML 
	
###Tarefas:

1. Instalar e configurar ambiente PHP
2. Instalar composer
3. Definir banco de dados e tabelas necessárias
4. Definir e construir interface de acesso administrativo
5. Definir e construir interface de usuário, incluindo a geração do PDF da DANFE
6. Montar script e classes para a realização das terefas necessárias
7. Montar testes unitários para garantir funcionamento em atualizações futuras

>NOTA: ainda existem muitas duvidas que teremos que esclarecer nessa fase de design desse projeto, como:
>>Vamos criar uma base de dados para cada cliente ou não ?

>Em algum momento vamos usar acesso a disco ou apenas o banco de dados ?
>>Isso dentre muitas outras duvidas!

>NOTA: A versão atual do NFePHP (v4.0 ou v4.1), faz uso de disco e isso somente será mudado a partir da versão v5.0 com previsão de lançamento de release apenas no segundo semestre.

