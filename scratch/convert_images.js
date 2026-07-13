import fs from 'fs';
import path from 'path';
import sharp from 'sharp';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const dir = path.join(__dirname, '../public/images');

async function processDirectory(currentDir) {
    const files = fs.readdirSync(currentDir);
    for (const file of files) {
        const fullPath = path.join(currentDir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            await processDirectory(fullPath);
            continue;
        }

        const ext = path.extname(file).toLowerCase();
        if (!['.png', '.jpg', '.jpeg'].includes(ext)) continue;

        const name = path.basename(file, ext);
        const destPath = path.join(currentDir, `${name}.webp`);

        if (fs.existsSync(destPath)) {
            console.log(`WebP version already exists for ${fullPath}, skipping.`);
            continue;
        }

        console.log(`Converting ${fullPath} to WebP...`);
        try {
            let pipeline = sharp(fullPath);
            const metadata = await pipeline.metadata();

            // Resize extremely large images if they exceed 1600px width
            if (metadata.width && metadata.width > 1600) {
                console.log(`Resizing from ${metadata.width}px width to 1600px...`);
                pipeline = pipeline.resize({ width: 1600, withoutEnlargement: true });
            }

            await pipeline
                .webp({ quality: 80, effort: 6 })
                .toFile(destPath);

            const origSize = fs.statSync(fullPath).size;
            const webpSize = fs.statSync(destPath).size;
            const savings = Math.round((1 - (webpSize / origSize)) * 100);
            console.log(`Saved: ${savings}% savings (${Math.round(origSize / 1024)}KB -> ${Math.round(webpSize / 1024)}KB)`);
        } catch (err) {
            console.error(`Error converting ${fullPath}:`, err);
        }
    }
}

async function run() {
    await processDirectory(dir);
    console.log('Conversion complete!');
}

run();
