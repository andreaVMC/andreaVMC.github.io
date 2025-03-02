const fs = require('fs').promises;
const path = require('path');

// Cartella principale da cui partire
const directoryPath = './docs';

// Funzione per esplorare ricorsivamente le cartelle e ottenere i file PDF
async function exploreDirectory(directory) {
  try {
    // Legge tutti i file e le cartelle nella directory
    const files = await fs.readdir(directory);
    
    // Array per memorizzare i percorsi relativi dei file PDF
    const filePaths = [];

    // Esplora ogni elemento (file o cartella)
    for (const file of files) {
      const filePath = path.join(directory, file);
      
      // Ottiene le informazioni sul file o sulla cartella
      const stats = await fs.stat(filePath);
      
      // Se è una cartella, chiama la funzione ricorsiva per esplorarla
      if (stats.isDirectory()) {
        const nestedFilePaths = await exploreDirectory(filePath); // Esplora la sottocartella
        filePaths.push(...nestedFilePaths); // Aggiungi i file trovati nelle sottocartelle
      } else if (path.extname(file).toLowerCase() === '.pdf') {
        // Se è un file PDF, rimuove l'estensione '.pdf' dal nome del file
        const relativePath = path.relative('./docs', filePath); // Percorso relativo senza 'docs/'
        const fileNameWithoutExtension = path.basename(relativePath, '.pdf'); // Rimuove l'estensione
        filePaths.push(relativePath.replace('.pdf', '')); // Aggiungi il percorso relativo senza estensione
      }
    }

    return filePaths; // Ritorna i file PDF trovati
  } catch (err) {
    console.error('Errore nell\'esplorazione della cartella:', err);
    return [];
  }
}

// Funzione per generare il file JSON
async function generateJSON() {
  try {
    // Esplora la cartella principale
    const filePaths = await exploreDirectory(directoryPath);
    
    // Crea il contenuto JSON
    const jsonContent = JSON.stringify(filePaths, null, 2);
    
    // Salva il JSON in un file
    await fs.writeFile('./files.json', jsonContent, 'utf8');
    console.log('File JSON generato con successo!');
  } catch (err) {
    console.error('Errore nel salvataggio del file JSON:', err);
  }
}

// Esegui la funzione per generare il file JSON
generateJSON();
