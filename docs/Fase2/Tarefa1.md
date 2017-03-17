# FASE 2

### Objetivo:

Baixar automaticamente todas as NFe e outros documentos fiscais destinados aos clientes cadastrados

## Tarefa 1 - Estabelecer tabelas necessárias na base de dados

O processo de download dos documentos destinados, está baseado no webservice DFe cujo manual pode ser obtido na [Nota Técnica 2014.002 - v1.02](https://www.nfe.fazenda.gov.br/portal/exibirArquivo.aspx?conteudo=VcEhGZODuo4=).

O processo básico se resume ao seguinte:

- Para cada CNPJ cadastrado na tabela cadastros (que são os destinatários a serem pesquisados), serão feitas uma série de buscas iterativas (em loop), limitada a 50 chamadas, com intervalos adicionais entre cada solicitação de 3 segundos, para obter os dados dos documentos destinados, a cada 2 horas no mínimo.

>*NOTA: Existe um controle de consumo desse serviço por parte da Receita então esse é o motivo para os intervalos tanto entre as chamadas como entre as pesquisas em si.*
 
- Cada chamada pode retornar de ZERO até 50 documentos destinados, entre eventos, nfes, protocolos de eventos e resumos de documentos (sendo um máximo de 250 registros por pesquisa).

- Cada um desses retornos da Receita Federal (as buscas são feitas no Ambiente Nacional e não na SEFAZ autorizadora), recebe por parte da Receita um numero de controle denominado NSU.

- Esses NSU e seus respectivos conteudos são salvos na tabela aenet_nfe.nsu.

- O conteúdo desses retornos são analisados e extraídos para suas respectivas bases finalizadas, aenet_nfe.eventos ou aenet_nfe.nfes, e no caso especifico de seu tipo ser "resNFe_v1.00.xsd", que indica apenas um resumo de uma NFe emitida para o CNPJ do destinatário, ainda será necessário que seja enviado um evento de "ciência da operação" para que essa NFe seja baixada posteriormente.


### Tabela aenet_nfe.nsus

Essa é uma tabela transitória, é continuamente alimentada e limpa quando o seu conteúdo não mais for necesssário. 

```mysql
--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Remover se existir tabela anterior `dfe_nsus`
--

DROP TABLE IF EXISTS `dfe_nsus`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `dfe_nsus`
-- Controle de dados recebidos do webservice de DFe
-- Responsável : Roberto
--

CREATE TABLE `dfe_nsus` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da Tabela',
  `id_empresa` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresas (AENET)',
  `nsu` int(11) NOT NULL COMMENT 'Id do registro (RECEITA)',
  `tipo` varchar(250) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Tipo de registro NSU (RECEITA)',
  `manifestar` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Pendencia de manifestação tipo RESUMO',
  `content` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Conteúdo do NSU, já descompactado',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `dfe_nsus`
--
```
Ao iniciar o processo de busca de destinadas o sistema lê o maior NSU contido na tabela para um determinado "id_empresa", e irá usar essa informação para buscar a sequencia seguinte de dados da Receita.

Antes de carregar a tabela com os dados obtidos, o sistema efetua uma limpeza, deletando todos os NSU's desse "id_empresa", onde o campo "manifestar" seja igual a "0", ou seja campos que não são mais necessários.

O sistema então carrega essa tabela, com todos os dados obtidos nessa pesquisa, caso o dado seja um resumo de nfe "resNFe_v1.00.xsd", a flag "manifestar" é setada como "1" ou seja pendente, ou setada ara zero nos demais casos.

Após o final do processo essa tabela é lida e os dados "prontos" (onde "manifestar" é igual a ZERO), são transferidos para as tabelas destino e caso haja pendência de manifestação a mesma é realizada. Em caso de sucesso da manifestação de destinatário, a flag "manifestar" é alterada para zero, para permitir a sua remoção na próxima pesquisa.
