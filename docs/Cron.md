# Configuração do CRON

Para configurar a execução dos jobs relativos aos processo de: Status, DFe e NFe.

> NOTA: Como essas tarefas serão executadas com o PHP-CLI tenha a certeza que o sistema operacional consegue encontrar o executável do PHP.
> Para fazer isso teste primeiro o comando via terminal e verifique que tudo tenha sido executado como previsto.

## Tarefa de busca do status das autorizadoras

Vá para a pasta onde ficam os agendamentos do cron

```bash
cd /etc/cron.d
```
Crie um arquivo com o primeiro set de comandos CRON

```bash
nano job_status
```

Insira os comandos

```bash
# Tarefa de busca do status dos serviços das autorizadoras
# Essa tarefa será executada
# */15  = a cada 15 minutos,
# 6-21 = das 6 às 21 horas,
# *    = todos os dias,
# *    = todos os meses,
# 1-6  = mas apenas de segunda a sábado
*/15 6-21 * * 1-6 root php /var/www/aenet/jobs/job_status.php &> /dev/null
```

## Tarefa de busca de documentos destinados DFe

Vá para a pasta onde ficam os agendamentos do cron

```bash
cd /etc/cron.d
```

Crie um arquivo com o primeiro set de comandos CRON

```bash
nano job_dfe
```

Insira os comandos

```bash
# Tarefa de busca de documentos destinados NFe
# Essa tarefa será executada
# 1   = ao primeiro 1 minuto,
# 6,10,14,18 = a cada 4 horas,
# *   = todos os dias,
# *   = todos os meses,
# 1-5 = mas apenas de segunda a sexta-feira
1 6,10,14,18 * * 1-5 root php /var/www/aenet/jobs/job_dfe.php &> /dev/null
```


```bash
# Tarefa de busca de documentos destinados CTe
# Essa tarefa será executada
# 1   = ao primeiro 1 minuto,
# 8,12,16,20 = a cada 4 horas,
# *   = todos os dias,
# *   = todos os meses,
# 1-5 = mas apenas de segunda a sexta-feira
30 4,8,12,16,20 * * 1-5 root php /var/www/aenet/jobs/job_dfe_cte.php &> /dev/null
```

## Tarefa de envio das NFe

Vá para a pasta onde ficam os agendamentos do cron

```bash
cd /etc/cron.d
```

Crie um arquivo com o primeiro set de comandos CRON

```bash
nano job_nfe
```
Insira os comandos

```bash
# Tarefa de envio das NFe geradas
# Essa tarefa será executada 
# */2  = a cada 2 minutos,
# 6-21 = das 6 às 21 horas,
# *    = todos os dias,
# *    = todos os meses,
# 1-6  = mas apenas de segunda a sábado 
*/2 6-21 * * 1-6 root php /var/www/aenet/jobs/job_nfe.php &> /dev/null
```

## Tarefa de autorixação as NFe

Vá para a pasta onde ficam os agendamentos do cron

```bash
cd /etc/cron.d
```

Crie um arquivo com o primeiro set de comandos CRON

```bash
nano job_nfe_consulta
```
Insira os comandos

```bash
# Tarefa de consulta e autorização das NFe geradas
# Essa tarefa será executada 
# */3  = a cada 3 minutos,
# 6-21 = das 6 às 21 horas,
# *    = todos os dias,
# *    = todos os meses,
# 1-6  = mas apenas de segunda a sábado 
*/3 6-21 * * 1-6 root php /var/www/aenet/jobs/job_nfe_consulta.php &> /dev/null
```



## Tarefa de criação dos DANFE 

Vá para a pasta onde ficam os agendamentos do cron

```bash
cd /etc/cron.d
```

Crie um arquivo com o primeiro set de comandos CRON

```bash
nano job_danfe
```
Insira os comandos

```bash
# Tarefa de criação dos DANFES
# Essa tarefa será executada 
# */3  = a cada 3 minutos,
# 6-21 = das 6 às 21 horas,
# *    = todos os dias,
# *    = todos os meses,
# 1-6  = mas apenas de segunda a sábado 
*/3 6-21 * * 1-6 root php /var/www/aenet/jobs/job_danfe.php &> /dev/null
```

## Tarefa de envio dos emails aos destinatários

Vá para a pasta onde ficam os agendamentos do cron

```bash
cd /etc/cron.d
```

Crie um arquivo com o primeiro set de comandos CRON

```bash
nano job_mail
```
Insira os comandos

```bash
# Tarefa de criação dos DANFES
# Essa tarefa será executada 
# */4  = a cada 4 minutos,
# 6-21 = das 6 às 21 horas,
# *    = todos os dias,
# *    = todos os meses,
# 1-6  = mas apenas de segunda a sábado 
*/4 6-21 * * 1-6 root php /var/www/aenet/jobs/job_danfe.php &> /dev/null
```


**LIMPEZA**
Os logs irão crescer indefinidamente se não forem limpos periódicamente, por isso será estabelecido um CRON JOB para essa limpeza que irá rodar todo o dia 1 as 00:05.

```bash
nano job_clearlogs
```

Insira os comandos

```bash
# Tarefa de limpeza dos arquivos de log
# Essa tarefa será executada
# 5  = aos 5 minutos,
# 0  = as 0 horas,
# 1  = no dia 1,
# *  = todos os meses,
# *  = seja em que dia da semana for
5 0 1 * * root /var/www/aenet/jobs/job_clearlogs.sh &> /dev/null
```

