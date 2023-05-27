import PySimpleGUI as sg

layout = [
    [sg.Button('|\\', button_color=('black', 'white'), size=(10, 5)),
     sg.Button('/\\', button_color=('black', 'white'), size=(10, 5), enable_events=True),
     sg.Button('/|', button_color=('black', 'white'), size=(10, 5))],
    [sg.Button('<', button_color=('black', 'white'), size=(10, 5)),
     sg.Button('default', button_color=('black', 'white'), size=(10, 5)),
     sg.Button('>', button_color=('black', 'white'), size=(10, 5))],
    [sg.Button('|/', button_color=('black', 'white'), size=(10, 5)),
     sg.Button('\\/', key='\\/', button_color=('black', 'white'), size=(10, 5), enable_events=True),
     sg.Button('\\|', button_color=('black', 'white'), size=(10, 5))]
]

window = sg.Window('Controller', layout, size=(300, 300))

while True:
    event, values = window.read()
    if event == sg.WIN_CLOSED:
        break
    elif event == '\\/':
        print('indietro');
    elif event == '/\\':
        print('avanti')
    elif event == '<':
        print('destra')
    elif event == '>':
        print('sinistra')
    if event == '|\\':
        print('avanti destra')
    elif event == '/|':
        print('avanti sinistra')
    elif event == '|/':
        print('indietro destra')
    elif event == '\\|':
        print('indietro sinistra')
    elif event=='default':
        print("default")
window.close()
