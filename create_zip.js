const fs = require('fs');
const path = require('path');
const zlib = require('zlib');

const files = [
  { source: 'public/js/vue/entry-client.js', zipPath: 'public/js/vue/entry-client.js' },
  { source: 'public/js/vue/entry-server.js', zipPath: 'public/js/vue/entry-server.js' },
  { source: 'public/mix-manifest.json', zipPath: 'public/mix-manifest.json' },
  { source: 'resources/assets/js/vue/components/Checkout.vue', zipPath: 'resources/assets/js/vue/components/Checkout.vue' }
];

const zipPath = 'checkout-production.zip';
if (fs.existsSync(zipPath)) fs.unlinkSync(zipPath);

const { Deflate } = zlib;
const entries = [];
let offset = 0;

for (const file of files) {
  if (!fs.existsSync(file.source)) {
    console.error('Missing:', file.source);
    process.exit(1);
  }
  const data = fs.readFileSync(file.source);
  const name = file.zipPath.replace(/\\/g, '/');
  
  entries.push({
    name,
    data,
    offset,
    size: data.length,
    compressedSize: 0,
    crc32: crc32(data)
  });
  
  offset += data.length;
}

const deflate = new Deflate({ level: 9 });
deflate.write(Buffer.concat(entries.map(e => e.data)));
deflate.close();

const compressed = deflate.result;

let centralDir = '';
let centralDirSize = 0;
let localHeaderOffset = 0;

for (const entry of entries) {
  const nameBytes = Buffer.from(entry.name, 'utf8');
  
  const localHeader = Buffer.alloc(30 + nameBytes.length);
  localHeader.writeUInt32LE(0x04034b50, 0);
  localHeader.writeUInt16LE(20, 4);
  localHeader.writeUInt16LE(0, 6);
  localHeader.writeUInt16LE(0, 8);
  localHeader.writeUInt16LE(8, 9);
  localHeader.writeUInt16LE(0, 10);
  localHeader.writeUInt32LE(entry.crc32, 11);
  localHeader.writeUInt32LE(entry.size, 15);
  localHeader.writeUInt32LE(compressed.length, 19);
  localHeader.writeUInt16LE(nameBytes.length, 23);
  localHeader.writeUInt16LE(0, 25);
  localHeader.writeUInt32LE(localHeaderOffset, 26);
  nameBytes.copy(localHeader, 30);
  
  localHeaderOffset += localHeader.length;
  
  const centralDirEntry = Buffer.alloc(46 + nameBytes.length);
  centralDirEntry.writeUInt32LE(0x02014b50, 0);
  centralDirEntry.writeUInt16LE(20, 4);
  centralDirEntry.writeUInt16LE(20, 6);
  centralDirEntry.writeUInt16LE(0, 8);
  centralDirEntry.writeUInt16LE(8, 9);
  centralDirEntry.writeUInt16LE(0, 10);
  centralDirEntry.writeUInt32LE(entry.crc32, 11);
  centralDirEntry.writeUInt32LE(entry.size, 15);
  centralDirEntry.writeUInt32LE(compressed.length, 19);
  centralDirEntry.writeUInt16LE(nameBytes.length, 23);
  centralDirEntry.writeUInt16LE(0, 25);
  centralDirEntry.writeUInt16LE(0, 25);
  centralDirEntry.writeUInt16LE(0, 27);
  centralDirEntry.writeUInt16LE(0, 29);
  centralDirEntry.writeUInt16LE(0, 31);
  centralDirEntry.writeUInt32LE(0, 33);
  centralDirEntry.writeUInt32LE(0, 37);
  centralDirEntry.writeUInt32LE(0, 41);
  centralDirEntry.writeUInt32LE(0, 45);
  nameBytes.copy(centralDirEntry, 46);
  
  centralDir += centralDirEntry.toString('binary');
  centralDirSize += centralDirEntry.length;
}

const endOfCentralDir = Buffer.alloc(22);
endOfCentralDir.writeUInt32LE(0x06054b50, 0);
endOfCentralDir.writeUInt16LE(0, 4);
endOfCentralDir.writeUInt16LE(0, 6);
endOfCentralDir.writeUInt16LE(entries.length, 8);
endOfCentralDir.writeUInt16LE(entries.length, 10);
endOfCentralDir.writeUInt32LE(centralDirSize, 12);
endOfCentralDir.writeUInt32LE(localHeaderOffset, 16);
endOfCentralDir.writeUInt16LE(0, 20);

const zipBuffer = Buffer.concat([
  Buffer.concat(entries.map((entry, i) => {
    const nameBytes = Buffer.from(entry.name, 'utf8');
    const localHeader = Buffer.alloc(30 + nameBytes.length);
    localHeader.writeUInt32LE(0x04034b50, 0);
    localHeader.writeUInt16LE(20, 4);
    localHeader.writeUInt16LE(0, 6);
    localHeader.writeUInt16LE(0, 8);
    localHeader.writeUInt16LE(8, 9);
    localHeader.writeUInt16LE(0, 10);
    localHeader.writeUInt32LE(entry.crc32, 11);
    localHeader.writeUInt32LE(entry.size, 15);
    localHeader.writeUInt32LE(compressed.length, 19);
    localHeader.writeUInt16LE(nameBytes.length, 23);
    localHeader.writeUInt16LE(0, 25);
    const startOffset = entries.slice(0, i).reduce((sum, e) => sum + 30 + Buffer.from(e.name, 'utf8').length + e.size, 0);
    localHeader.writeUInt32LE(startOffset, 26);
    nameBytes.copy(localHeader, 30);
    return Buffer.concat([localHeader, Buffer.from(entry.data)]);
  })),
  Buffer.from(centralDir, 'binary'),
  endOfCentralDir
]);

fs.writeFileSync(zipPath, zipBuffer);
console.log('ZIP created:', zipPath);
const hash = crypto.createHash('sha256').update(fs.readFileSync(zipPath)).digest('hex');
console.log('SHA256:', hash);

function crc32(data) {
  let crc = 0xFFFFFFFF;
  const table = makeCrcTable();
  for (let i = 0; i < data.length; i++) {
    crc = (crc >>> 8) ^ table[(crc ^ data[i]) & 0xFF];
  }
  return (crc ^ 0xFFFFFFFF) >>> 0;
}

function makeCrcTable() {
  const table = new Array(256);
  for (let n = 0; n < 256; n++) {
    let c = n;
    for (let k = 0; k < 8; k++) {
      c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
    }
    table[n] = c;
  }
  return table;
}

const crypto = require('crypto');
