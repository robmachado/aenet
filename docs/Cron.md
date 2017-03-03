#Configuração do CRON

Para configurar a execução dos jobs relativos aos processo de DFe e envio de NFe

>NOTA: Como essas tarefas serão executadas com o PHP-CLI tenha a certeza que o sistema operacional consegue encontar o executavel do PHP.
>Para fazer isso teste primeiro o comando via terminal e verifique que tudo tenha sido executado como previsto. 

##Tarefa de busca de documentos destinados DFe

Vá para a pasta onde ficam os agendamentos do cron

```bash
cd /etc/cron.d
```

Crie um arquivo com o primeiro set de comandos CRON

```bash
nano taskdfe
```

Insira os comandos
 
```bash
# Tarefa de busca de documentos destinados
# Essa tarefa será executada
# 1   = ao primeiro 1 minuto,
# */2 = a cada 2 horas,
# *   = todos os dias,
# *   = todos os meses,
# 1-5 = mas apenas de segunda a sexta-feira
1 */2 * * 1-5 root php /var/www/aenet/jobs/processdfe.php &> /dev/null
```

##Tarefa de envio das NFe, Cancelamentos e Eventos 

Vá para a pasta onde ficam os agendamentos do cron

```bash
cd /etc/cron.d
```

Crie um arquivo com o primeiro set de comandos CRON

```bash
nano tasknfe
```
Insira os comandos

```bash
# Tarefa de envio das NFe geradas
# Essa tarefa será executada 
# */2  = a cada 2 minutos,
# 8-21 = das 8 às 21 horas,
# *    = todos os dias,
# *    = todos os meses,
# 1-6  = mas apenas de segunda a sábado 
*/2 8-21 * * 1-6 root php /var/www/aenet/jobs/processnfe.php &> /dev/null
```

