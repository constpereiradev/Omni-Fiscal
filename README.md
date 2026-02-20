O **OmniFiscal** é um middleware desenvolvido para automatizar o fluxo fiscal entre plataformas de e-commerce e a API da **WebMania**. Ele atua como um tradutor de dados, garantindo que payloads de origem internacional (WooCommerce) sejam convertidos em notas fiscais brasileiras (NF-e/NFS-e).

- Requisitos técnicos
    - Laragon - https://laragon.org/docs ou ambiente PHP 8.2+
        
        **Configuração Rápida**
        
        - Instalar dependências
            
            `composer install`
            
        - Executar migrações
            
            `php artisan migrate` 
            
        - Iniciar o Worker da Fila
            
            `php artisan queue:work`
        ****
        
        Um ambiente de desenvolvimento universal. É excelente para criar e gerenciar aplicações web modernas. Seu foco é o desempenho, sendo projetado com base em estabilidade, simplicidade, flexibilidade e liberdade.
        
- Banco de dados
    - Pedidos
        
        Tabela: **orders**
        
        A tabela principal foi projetada para suportar auditoria de dados brutos e rastreamento de status fiscal em tempo real.
        
        | **Campo** | **Tipo** | **Descrição** |
        | --- | --- | --- |
        | `id` | UUID | Chave primária |
        | `external_id` | String | Identificador externo do pedido |
        | `raw_data` | JSONB | Dados brutos do pedido |
        | `origin` | String | Origem do pedido |
        | `customer_id` | String | Identificador do cliente (opcional) |
        | `order_key` | String | Chave do pedido (opcional) |
        | `currency` | String | Moeda do pedido (opcional) |
        | `billing_name` | String | Nome de cobrança (opcional) |
        | `billing_address` | String | Endereço de cobrança (opcional) |
        | `shipping_name` | String | Nome de entrega (opcional) |
        | `shipping_address` | String | Endereço de entrega (opcional) |
        | `items` | Text | Itens do pedido (opcional) |
        | `total` | Decimal(10, 2) | Total do pedido (opcional) |
        | `discount_total` | Decimal(10, 2) | Total de descontos (opcional) |
        | `shipping_total` | Decimal(10, 2) | Total de frete (opcional) |
        | `status` | String | Status do pedido (opcional) |
        | `last_error` | String | Último erro (opcional) |
        | `timestamps` | Timestamp | Data de criação e atualização do pedido |
- Fluxo da aplicação
    1. **Ingestão via Webhook:** Recebe requisições no endpoint `POST /api/v1/orders`.
    2. **Strategy Pattern:** O sistema identifica a origem e utiliza a classe `WooCommerceStrategy` para extrair CPF, número e bairro, que nativamente não existem no padrão internacional do WooCommerce.
    3. **Processamento Assíncrono:** Um **Job** é disparado para a fila (`ProcessInvoiceJob`), garantindo que instabilidades na SEFAZ ou na API externa não travem a experiência do usuário.
    4. **Comunicação WebMania:** O `FiscalService` consome o endpoint `/1/nfe/emissao/`, injetando códigos de serviço (CNAE/ISS) configurados no lojista.
    
    - Webhook
        
        Os webhooks são configurados na aba de configuração avançada do plugin (neste caso, Woocomerce). Lá, é informado o endpoint que será chamado e a ação (trigger) que irá disparar o evento.
        
        - Triggers
            - Criação de pedido
                - Quando um pedido é criado, o endpoint http://omni-fiscal.test/api/v1/orders ****é chamado. Ele possui as seguintes responsabilidades:
                    1. Checa de onde a requisição está vindo (WooComerce, Shopify, etc)
                    2. Trata os dados retornados a depender de onde vem a requisição
                    3. Armazena na tabela **orders** informações relevantes sobre o pedido
                    4. Um novo Job é adicionado na fila para emissão de nota fiscal
                    5. É realizada uma chamada para a API de geração de notas fiscais da WebMania (https://webmania.com.br/docs/rest-api-nfe/)
                    6. Se sucesso, a NF é gerada.
            

Esta API tem como princípio manter a escalabilidade, pensando em futuras integrações de plataformas de e-commerce (arquitetura Strategy), resiliência, implementando filas e código limpo, separando lógica de recepção nos **Controllers**, transformação nas **Strategies** e regras de negócio nos **Services**.

Confira aqui os endpoints desta API: https://www.postman.com/martian-firefly-354203/omni-fiscal/collection/25684396-606a9d77-f605-4efe-9b4a-3a7002563354/?action=share&creator=25684396