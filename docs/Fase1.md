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

1. [Instalar e configurar ambiente PHP](Configuracao_Debian_8.md)
2. [Instalar composer](Configuracao_Debian_8.md)
3. [Definir banco de dados e tabelas necessárias](Fase1_Tarefa3.md)
4. [Estabelecer os formatos dos TXT quando não se tratar de uma NFe](Fase1_Tarefa4.md)
5. [Definir e construir interface de acesso administrativo](Fase1_Tarefa5.md)
6. [Definir e construir interface de usuário, incluindo a geração do PDF da DANFE](Fase1_Tarefa6.md)
7. [Montar script e classes para a realização das terefas necessárias](Fase1_Tarefa7.md)
8. [Montar testes unitários para garantir funcionamento em atualizações futuras](Fase1_Tarefa8.md)

>NOTA: ainda existem muitas duvidas que teremos que esclarecer nessa fase de design desse projeto, como:
>>Vamos criar uma base de dados para cada cliente ou não ?

>Em algum momento vamos usar acesso a disco ou apenas o banco de dados ?
>>Isso dentre muitas outras duvidas!

>NOTA: A versão atual do NFePHP (v4.0 ou v4.1), faz uso de disco e isso somente será mudado a partir da versão v5.0 com previsão de lançamento de release apenas no segundo semestre.

