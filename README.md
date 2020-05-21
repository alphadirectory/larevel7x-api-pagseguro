###Dependências

A única dependencia instalada nesse projeto é da propria pagseguro

Comando usado para instalar:

`composer require pagseguro/pagseguro-php-sdk`

seguindo o projeto homologado pela propria  pagSeguro. Veja a documentação:
https://github.com/pagseguro/pagseguro-php-sdk

**OBS.: ** Seu projeto já está configurado. Os dados acima são apenas pra conhecimento.


### instalação
- Instale as dependências
` composer install`

- Crie um arquivo .env e copie o modelo encontrado em exemple.env

- Configure os apontamentos para o seu banco de dados

- Execute a migração
`php artisan migrate:fresh`

- Em seu env, configure as cheves com prefixo PAGSEGURO

#FRONT

A pagSeguro conta com uma biblioteca de front que verifica e prepara os 
dados do cliente para executar a transação

Você pode ver todas as chamadas e como elas se comportam, em uma view localizada em:
resources/views/exemplo-pagseguro.blade.php

### Payload

{
	"sessionId": "c10d7b28e9744e37914f15811249d9bc",
	"amount": "200.00",
	"cardNumber": "000 0000 0000 0000",
	"cardBrand": { // esse objeto é formado pela lig js do pagSeguro
		"name": "mastercard",
		"bin": 543882,
		"cvvSize": 3,
		"expirable": true,
		"international": false,
		"validationAlgorithm": "LUHN",
		"config": {
			"acceptedLengths": [
				16
			],
			"hasDueDate": true,
			"hasCvv": true,
			"hasPassword": false,
			"securityFieldLength": 3
		}
	},
	"mode": "credito",
	"cardToken": "d06d60b0b8fe42aaa513ede4cf708fe7",
	"banco": "",
	"hash": "f4bf9b67b92f921863f9fda987663285ecadb2bd5d9a85945d9f4bb9ec26891c"
}