# symfony_test
Desafio Programador PHP - Brasil TecPar

Olá! Eu sou o Luiz Gustavo Miquelão Scaff e essa é a minha resolução para o Desafio Programador PHP da Brasil TecPar.

//-------------------------------------------------------------------------------------------------------------------------------------

Rota POST para geração do hash: /api/hash_results/generate/{stringDeEntrada}

Exemplo: /api/hash_results/generate/brasiltecpar

//-------------------------------------------------------------------------------------------------------------------------------------

Command para criação em série dos hashes: hash_result:generate {stringDeEntrada} {numeroDeRequisições}

Exemplo: hash_result:generate brasiltecpar 20

//-------------------------------------------------------------------------------------------------------------------------------------

Rota GET para retorno dos resultados armazenados no banco de dados: api/hash_results/get_hash_results

//-------------------------------------------------------------------------------------------------------------------------------------

Rota GET para filtragem dos hashes armazenados no banco de dados por número máximo de tentativas: api/hash_results/get_hash_results/{numeroMáximoDeTentativas?}

Exemplo: api/hash_results/get_hash_results/50000

//-------------------------------------------------------------------------------------------------------------------------------------

Rota para geração do hash:

  -Essa rota recebe como parâmetro uma string que será utilizada para a geração do hash.

  -Para o throttling utiliza-se a ferramenta "Rate Limiter", do próprio Symfony, fixada em 10 requisições por minuto por IP. Se houver mais de 10 requisições no intervalo de um minuto, há o retorno de uma exceção "Too Many Attempts" e do código de erro 429 (Too Many Requests).

  -Para encontrar o hash, a string é enviada como parâmetro a outra função dedicada exclusivamente a essa tarefa.

  -A função em questão (FindHash) contém um "for" que repete até que um hash de início "0000" seja encontrado. Para a manipulação do hash e da key foi utilizada a ferramenta "String" do Symfony, e para a comparação dos quatro primeiros caracteres do hash com a string "0000", a função strcmp.

  -Para salvar os dados retornados da função "FindHash" no banco de dados, existe uma outra função (Save) que tem essa única finalidade.
  
  -Como o save dos dados também acontece a partir do "command", essa função é estática. Dessa forma, evita-se a criação de duas funções que teriam a mesma utilidade.
  
  -Após o save, o retorno será dos seguintes dados: "hash", "key", "attempts".

//-------------------------------------------------------------------------------------------------------------------------------------

Command para criação em série dos hashes:

  -Esse command tem um funcionamento similar à função para geração do hash via route. As diferenças cruciais são que, aqui, existe um "for" que repete de acordo com o número de requisições enviadas no segundo parâmetro do command, e que cada hash gerado se torna a string de entrada para a próxima requisição.
  
  -Para cada repetição do for, será chamada a mesma função "FindHash" citada anteriormente.
  
  -Quando o hash for encontrado, será chamada a mesma função "Save" citada anteriormente.
  
  -Para cada hash encontrado, linhas com as informações correntes são escritas no terminal (número da requisição, string de entrada, hash, chave encontrada e o número de tentativas para encontrar o hash.

//-------------------------------------------------------------------------------------------------------------------------------------

Rota para get dos hashes armazenados no banco de dados:

  -Essa rota retorna os campos "batch", "número da requisição", "string de entrada" e "chave encontrada".
  
  -O filtro por "número máximo de tentativas" é feito na url como parâmetro opcional. Se o parâmetro for preenchido, numérico e maior que 0, ele será aplicado, retornando apenas os registros cujas colunas "number_of_tries" tiverem valores inferiores ao informado na URL.
