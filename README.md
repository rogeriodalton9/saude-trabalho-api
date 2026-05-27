# 🏥 Saúde Trabalho API

API para importação de dados da Portaria GM/MS nº 5.674 de 1º de novembro de 2024 do Diário Oficial da União (DOU).

## 📋 Sobre

Esta API importa informações sobre:
- **Agentes e Fatores de Risco** relacionados ao trabalho
- **Listas de Doenças** relacionadas ao trabalho (com CID-10)
- **Relacionamentos** entre agentes, listas e códigos CID-10

## 🗄️ Banco de Dados

A aplicação utiliza 3 tabelas principais:

### 1. `agentes`
```
- id (PK)
- nome (string)
- timestamps
```

### 2. `listas`
```
- id (PK)
- nome (string)
- timestamps
```

### 3. `saude_danos`
```
- id (PK)
- CID10 (char)
- lista_id (FK)
- agente_id (FK)
- risco (string)
- timestamps
```

## 🚀 Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/rogeriodalton9/saude-trabalho-api.git
cd saude-trabalho-api
```

### 2. Instale as dependências
```bash
composer install
```

### 3. Configure o arquivo `.env`
```bash
cp .env.example .env
php artisan key:generate
```

Edite o `.env` com suas credenciais do banco de dados:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saude_trabalho
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Execute as migrations
```bash
php artisan migrate
```

## 📥 Importação de Dados

Para importar os dados da Portaria 5.674 do DOU, execute:

```bash
php artisan dou:import-portaria
```

Este comando irá:
1. ✅ Acessar a página do DOU
2. ✅ Extrair as listas de doenças
3. ✅ Extrair os agentes/fatores de risco
4. ✅ Extrair os códigos CID-10
5. ✅ Popular as 3 tabelas automaticamente
6. ✅ Evitar duplicatas

## 📊 Exemplos de Uso

### Buscar um agente
```php
$agente = Agente::where('nome', 'Cloreto de vinila')->first();
```

### Buscar doenças de um agente
```php
$agente = Agente::find(1);
$doenças = $agente->saudeDanos;
```

### Buscar agentes de uma lista
```php
$lista = Lista::find(1);
$agentes = $lista->saudeDanos;
```

## 📝 Modelos

### Agente
```php
$agente->saudeDanos(); // Relação HasMany
```

### Lista
```php
$lista->saudeDanos(); // Relação HasMany
```

### SaudeDano
```php
$saudeDano->agente();  // Relação BelongsTo
$saudeDano->lista();   // Relação BelongsTo
```

## 🔧 Requisitos

- PHP 8.2+
- Laravel 13
- MySQL ou PostgreSQL
- Composer

## 📦 Dependências

- `goutte/goutte` - Para web scraping
- `symfony/dom-crawler` - Para parsing de HTML

## 📄 Licença

MIT

## 👨‍💻 Autor

Rogério Dalton

## 📧 Suporte

Para dúvidas ou sugestões, abra uma issue no repositório.
