const http = require('http');
const mysql = require('mysql');

// Configuração de conexão (sem banco fixo)
const conn = mysql.createConnection({
  host: 'localhost',
  user: 'luizfelipe',
  password: 'senha'
});

const PORT = 8000;

const server = http.createServer((req, res) => {
  // Cabeçalhos comuns
  res.setHeader('Content-Type', 'application/json');
  res.setHeader('Access-Control-Allow-Origin', '*');

  // Rota GET: teste
  if (req.method === 'GET' && req.url === '/') {
    res.writeHead(200);
    res.end(JSON.stringify({ status: 'ok', mensagem: 'API funcionando' }));
    return;
  }

  // Rota POST
  if (req.method === 'POST' && req.url === '/') {
    let body = '';

    req.on('data', chunk => {
      body += chunk;
    });

    req.on('end', () => {
      try {
        const { query } = JSON.parse(body);
        if (!query) {
          res.writeHead(400);
          res.end(JSON.stringify({ erro: 'Query SQL não fornecida.' }));
          return;
        }

        conn.query(query, (err, results) => {
          if (err) {
            res.writeHead(400);
            res.end(JSON.stringify({ erro: err.message }));
          } else {
            res.writeHead(200);
            res.end(JSON.stringify({ sucesso: true, dados: results }));
          }
        });
      } catch (e) {
        res.writeHead(400);
        res.end(JSON.stringify({ erro: 'JSON inválido.' }));
      }
    });

    return;
  }

  // Rota não encontrada
  res.writeHead(404);
  res.end(JSON.stringify({ erro: 'Rota não encontrada.' }));
});

server.listen(PORT, () => {
  console.log(`🚀 Servidor rodando em http://localhost:${PORT}`);
});
