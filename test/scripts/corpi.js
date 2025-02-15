// scripts/corpi.js
const { Engine, Render, World, Bodies, Mouse, MouseConstraint, Events } = Matter;


export function anakin(n) {
    const objects = [];
    for (let i = 0; i < n; i++) {
        const obj = Bodies.rectangle(
            Math.random() * window.innerWidth,
            Math.random() * window.innerHeight,
            120, // Larghezza del rettangolo
            120, // Altezza del rettangolo
            {
                restitution: 1,
                frictionAir: 0.01,
                inertia: 5000,
                force: { 
                    x: (Math.random() * 0.15) * (Math.random() < 0.5 ? -1 : 1),
                    y: (Math.random() * 0.15) * (Math.random() < 0.5 ? -1 : 1)
                },
                render: {
                    sprite: {
                        texture: "img/anakin.png", 
                    },
                },
                chamfer: { radius: 15 },
                url: "#"
            }
        );
        objects.push(obj);
    }
    return objects; // Restituisce un array di oggetti
}

export function generatePortfolio() {
    const objects = [];
    objects.push(unipr());
    return objects;
}

function unipr(){
    return Bodies.rectangle(
        window.innerWidth / 2,
        window.innerHeight / 2,
        120, // Larghezza del rettangolo
        120, // Altezza del rettangolo
        {
            restitution: 1,
            frictionAir: 0.01,
            inertia: 5000,
            force: { 
                x: (Math.random() * 0.15) * (Math.random() < 0.5 ? -1 : 1),
                y: (Math.random() * 0.15) * (Math.random() < 0.5 ? -1 : 1)
            },
            render: {
                sprite: {
                    texture: "img/unipr.png", 
                },
            },
            chamfer: { radius: 15 },
            url: "../Archivio/Unipr/index.html"
        }
    )
}