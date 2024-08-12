function startConfetti() {
    tsParticles.load("tsparticles", {
        fullScreen: {
            enable: true
        },
        particles: {
            number: {
                value: 0
            },
            color: {
                value: [
                    "#FFFFFF", "#FF0000", "#FFA500", "#FFFF00", "#00FF00", 
                    "#0000FF", "#4B0082", "#EE82EE", "#FFC0CB", "#00FFFF"
                ]
            },
            shape: {
                type: ["circle", "square"]
            },
            opacity: {
                value: 0.7,
                animation: {
                    enable: true,
                    minimumValue: 0.1,
                    speed: 2,
                    sync: false
                }
            },
            size: {
                value: { min: 2, max: 5 }
            },
            rotate: {
                value: {
                    min: 0,
                    max: 360
                },
                direction: "random",
                animation: {
                    enable: true,
                    speed: 10
                }
            },
            move: {
                enable: true,
                gravity: {
                    enable: true,
                    acceleration: 9.81
                },
                speed: { min: 3, max: 8 },
                direction: "random",
                outModes: {
                    default: "destroy",
                    top: "none"
                },
                wobble: {
                    enable: true,
                    distance: 5,
                    speed: {
                        min: -1,
                        max: 1
                    }
                },
            }
        },
        emitters: [
            {
                position: {
                    x: 0,
                    y: 30
                },
                rate: {
                    delay: 0.1,
                    quantity: 5
                },
                angle: {
                    min: 0,
                    max: 30
                },
                size: {
                    width: 100,
                    height: 0
                },
            },
            {
                position: {
                    x: 100,
                    y: 30
                },
                rate: {
                    delay: 0.1,
                    quantity: 5
                },
                angle: {
                    min: 150,
                    max: 180
                },
                size: {
                    width: 100,
                    height: 0
                },
            }
        ]
    });
}

function startSchoolPrideConfetti() {
    const end = Date.now() + 3 * 1000;
    const colors = [
        "#bb0000", "#ffffff", "#228B22", "#FFD700", "#4169E1", "#FF8C00",
        "#32CD32", "#00FF00", "#008000", "#00FA9A", "#90EE90"
    ];
    
    function getRandomColors(count) {
        return Array.from({ length: count }, () => colors[Math.floor(Math.random() * colors.length)]);
    }

    (function frame() {
        confetti({
            particleCount: 2,
            angle: 60,
            spread: 55,
            origin: { x: 0 },
            colors: getRandomColors(2),
        });
        confetti({
            particleCount: 2,
            angle: 120,
            spread: 55,
            origin: { x: 1 },
            colors: getRandomColors(2),
        });
        if (Date.now() < end) {
            requestAnimationFrame(frame);
        }
    })();
}