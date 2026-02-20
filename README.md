# Integrador de Pedidos e NF-e

O objetivo é criar uma API em **Laravel** que simule o meio de campo entre uma loja virtual (como WooCommerce) e um emissor de notas fiscais, utilizando IA para categorizar impostos e garantir a integridade dos dados.

- Requisitos técnicos
    - Laragon - https://laragon.org/docs
        
        Um ambiente de desenvolvimento universal. É excelente para criar e gerenciar aplicações web modernas. Seu foco é o desempenho, sendo projetado com base em estabilidade, simplicidade, flexibilidade e liberdade.
        
- Banco de dados
    - Pedidos
        
        Tabela: **orders**
        
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
        | `items` | JSONB | Itens do pedido (opcional) |
        | `total` | Decimal(10, 2) | Total do pedido (opcional) |
        | `discount_total` | Decimal(10, 2) | Total de descontos (opcional) |
        | `shipping_total` | Decimal(10, 2) | Total de frete (opcional) |
        | `status` | String | Status do pedido (opcional) |
        | `last_error` | String | Último erro (opcional) |
        | `timestamps` | Timestamp | Data de criação e atualização do pedido |
- Fluxo da aplicação
    - Webhook
        
        Os webhooks são configurados na aba de configuração avançada do plugin (neste caso, Woocomerce). Lá, é informado o endpoint que será chamado e a ação (trigger) que irá disparar o evento.
        
        - Triggers
            - Criação de pedido
                - Quando um pedido é criado, o endpoint http://omni-fiscal.test/api/v1/orders ****é chamado. Ele possui as seguintes responsabilidades:
                    1. Checa de onde a requisição está vindo (WooComerce, Shopify, etc)
                    2. Trata os dados retornados a depender de onde vem a requisição
                    3. Armazena na tabela **orders** informações relevantes sobre o pedido