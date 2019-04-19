// migrate.js
// Migration tool for helping to transition from MongoDB to SQLite3
const fs = require('fs');

// Read JSON dump
const text = fs.readFileSync('paste.json', 'utf8');
const lines = text.split('\n');

// Connect to SQlite
const dblite = require('dblite');
const db = dblite('./paste.db');

// Insert rows
for (let line of lines) {
  if (!line) {
    continue;
  }
  const obj = JSON.parse(line);
  if (obj.version === 2) {
    db.query('INSERT INTO paste VALUES(?, ?, ?, ?, ?)', [
      obj.uid,
      obj.syntax,
      obj.text,
      obj.user.ip,
      obj.dateCreated.$date
        .replace('T', ' ')
        .substr(0, 19),
    ]);
    continue;
  }
  if (obj.version === 3) {
    db.query('INSERT INTO paste VALUES(?, ?, ?, ?, ?)', [
      obj.uid,
      obj.syntax,
      obj.text,
      obj.submitterIp,
      obj.createdAt.$date
        .replace('T', ' ')
        .substr(0, 19),
    ]);
    continue;
  }
}
