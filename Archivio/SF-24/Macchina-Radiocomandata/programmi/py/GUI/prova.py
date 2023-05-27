import PySimpleGUI as sg

# Definiamo il layout della finestra
layout = [
    [sg.Text("Premi il pulsante per continuare:")],
    [sg.Button("Hold", key="-HOLD-", button_color=("white", "black"))]
]

# Creiamo la finestra
window = sg.Window("Hold Button", layout)

# Ciclo principale del programma
while True:
    event, values = window.read(timeout=100)

    # Se l'utente chiude la finestra
    if event == sg.WIN_CLOSED:
        break

    # Se il pulsante viene premuto
    if event == "-HOLD-":
        # Ripetiamo l'istruzione finché il pulsante è tenuto premuto
        while True:
            event, values = window.read(timeout=100)

            # Se l'utente rilascia il pulsante
            if event != "-HOLD-":
                break

            # Altrimenti, eseguiamo l'istruzione
            print("Sto eseguendo l'istruzione finché il pulsante è tenuto premuto.")

# Chiudiamo la finestra
window.close()
