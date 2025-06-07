const fastify = require('fastify')();

fastify.get('/ping', async () => {
  return { status: 'ok', message: 'pong' };
});

fastify.listen({ port: 3000, host: '0.0.0.0' }, (err) => {
  if (err) {
    console.error(err);
    process.exit(1);
  }
  console.log('Servidor Fastify rodando em http://localhost:3000');
});