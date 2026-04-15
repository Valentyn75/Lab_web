const http = require('http');
const fs = require('fs');
const path = require('path');

const PORT = 3000;

const server = http.createServer((req, res) => {
    console.log(`Запит: ${req.url}`);

    // Головна сторінка
    if (req.url === '/' || req.url === '/index.html') {
        fs.readFile(path.join(__dirname, 'index.html'), (err, data) => {
            if (err) {
                res.writeHead(500);
                return res.end('Помилка сервера');
            }
            res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
            res.end(data);
        });
    } 
    // Проста API-відповідь
    else if (req.url === '/api/weather') {
        res.writeHead(200, { 'Content-Type': 'application/json; charset=utf-8' });
        const weatherData = {
            city: "Київ",
            temp: 18,
            feelsLike: 16,
            condition: "Ясно",
            humidity: 65,
            wind: 3
        };
        res.end(JSON.stringify(weatherData));
    } 
    else {
        res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
        res.end('Сторінку не знайдено');
    }
});

server.listen(PORT, () => {
    console.log(`🚀 Сервер запущено на http://localhost:${PORT}`);
});