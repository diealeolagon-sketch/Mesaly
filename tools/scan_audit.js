const fs = require('fs');
const path = require('path');

function walk(dir, exts = ['.php', '.js', '.css']) {
    let results = [];
    const list = fs.readdirSync(dir);
    for (const item of list) {
        const full = path.join(dir, item);
        const stat = fs.statSync(full);
        if (stat.isDirectory()) {
            results = results.concat(walk(full, exts));
        } else if (exts.some(e => full.endsWith(e))) {
            results.push(full);
        }
    }
    return results;
}

const viewFiles = walk('app/Views', ['.php']);
console.log(`--- Analizando ${viewFiles.length} vistas ---`);

let totalHandlers = 0;
for (const f of viewFiles) {
    const content = fs.readFileSync(f, 'utf8');
    const regex = /on(?:click|change|input|submit|keyup|keydown)\s*=\s*["'][^"']+["']/gi;
    let m;
    const matches = [];
    while ((m = regex.exec(content)) !== null) {
        matches.push(m[0]);
    }
    if (matches.length > 0) {
        console.log(`[HANDLERS] ${path.relative(process.cwd(), f)} (${matches.length}):`);
        matches.forEach(h => console.log('   ', h.substring(0, 80)));
        totalHandlers += matches.length;
    }
}
console.log(`Total manejadores encontrados: ${totalHandlers}`);

console.log('\n--- Verificando nonces en <script> y <style> ---');
for (const f of viewFiles) {
    const content = fs.readFileSync(f, 'utf8');
    const tags = content.match(/<(script|style)[^>]*>/gi) || [];
    for (const tag of tags) {
        // Ignorar scripts con src externo si fuera el caso, pero en nuestro caso todo script o style en vista debe tener nonce
        if (!tag.includes('nonce=')) {
            console.log(`[SIN NONCE] ${path.relative(process.cwd(), f)}: ${tag}`);
        }
    }
}

// Buscar emojis
const emojiRegex = /[\u{1F300}-\u{1F9FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1FA00}-\u{1FAFF}]/u;
const allCodeFiles = walk('app').concat(walk('public/assets/js'));
console.log(`\n--- Analizando emojis en ${allCodeFiles.length} archivos ---`);
let totalEmojis = 0;
for (const f of allCodeFiles) {
    const lines = fs.readFileSync(f, 'utf8').split('\n');
    let fileEmojis = 0;
    lines.forEach((l, idx) => {
        if (emojiRegex.test(l)) {
            fileEmojis++;
            totalEmojis++;
        }
    });
    if (fileEmojis > 0) {
        console.log(`[EMOJIS] ${path.relative(process.cwd(), f)}: ${fileEmojis} líneas con emojis`);
    }
}
console.log(`Total líneas con emojis: ${totalEmojis}`);
