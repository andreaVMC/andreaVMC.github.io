data: 17/03/2023
    proposta di realizzare un progetto a scelta in gruppo per a materia di telecomunicazioni da parte dei docenti

data: 24/03/2023
    nel giro di una settimana abbiamo creato il gruppo formato da: Andrea Vaccaro, Kai Cheng Liu, Matteo Braguzzi, Michele Pedroni;
    sotto proposta di Andrea Vaccaro è stato deciso di scegliere il progetto di una macchinina radiocomandata, ancora ben da definire
    nei dettagli.

data: 31/03/2023
    Pratica in classe con l' uso del saldatore a stagno, risultati scadenti, e danneggiamento di un componente privato (non fornito dalla scuola): "Arduino nano" (vedere: "problemi-1.jpg" e 
    "problemi-2.jpg"), ridefinizione del materiale necessario per la realizzazione del progetto ed inizio degl' acquisti per procurarseli (sempre in maniera privata).
    Al momento si opta per una trasmissione wireless dei dati, attraverso il componenet "HC-05" utilizzato per per la trasmissione Bluetooth (probabile scelta definitiva vista la
    precedente esperienza avuta col componente); per quanto riguarda il lato Master si è sbilanciati verso l' utilizzo din programma java, dotato di una GUI (interfaccia grafica),
    per gestire la trasmissione ed il ricevimento dei dati con l' HC-05, collegato a sua volta allo slayer (per cui fino ad oggi si pensava di adottare l' Arduino nano), per cui ora si
    pensa di adottare un Arduino UNO R3, più semplice da adoperare rispetto all' Arduino nano; per quanto riguarda l'apparato del movimento della macchina l' idea attuale
    è quella di utilizzare due motorini elettrici a 5V per le ruote posteriori, e 2 servo motori per le ruote anteriori, cosi da avere una trazione posteriore e possibilità
    di sterzata sull'apparecchio. [utilizzo di un circuito stampato con NFC, per l' utilizzio di una chiave elettronica per sbloccare la macchina].





data: 11/05/2023
	

Primo prototipo macchina ferari con implementazione di trazione posteriore, i riscontri sono stati:
Potenza della power banck con 5 Volt sufficiente solo per due motori, ma il peso della vettura complessiva impegnato su solo due motori è troppo alto.

CONSIDERAZIONI
Rimuovere i due servo motori per il controllo sterzo e usiamo altri due motori in lineare per gestire il peso. TOTALE = 4 motori;
La sterzata del veicolo avviene fermando due dei quattro motori.



data: 12/05/2023

Tentativo fallito di retromarcia con tentativo di implementazione dell'integrato L293 necessario per invertire la corrente;
abbondato tentativo di retromarcia per via dell'insufficiente potenza dei motori.
Test dell'uso di transistor per regolare il passaggio di corrente indipendente tra zona motori destra e sinista --> TENTATIVO RIUSCITO con ottimi risultati;
TODO: aggiornamento programma ARDUINO


