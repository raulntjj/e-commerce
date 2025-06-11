const fastify = require('fastify')();

fastify.get('/', async () => {
  return {
    system: 'Notification Service - eCommerce',
    description: 'Serviço de gerenciamento de notificações para eCommerce',
    version: '1.0.0',
    status: 'Operacional'
  };
});

fastify.listen({ port: 3000, host: '0.0.0.0' }, (err) => {
  if (err) {
    console.error(err);
    process.exit(1);
  }
  console.log('Servidor Fastify rodando em http://localhost:3000');
});