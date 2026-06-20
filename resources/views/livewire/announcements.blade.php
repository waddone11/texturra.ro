<div>
    <div class="w-full h-8 p-2 bg-green-500-2 text-xs text-white px-4 mx-auto">
        <div class="marquee">
            <p class="mr-2 font-bold"><span class="text-black font-bold">Anunt:</span> Bun venit pe site-ul oficial Ambalaje catering. <span class="text-black font-bold">15% reducere la prima comanda.</span></p>
        </div>
    </div>
    <!-- CSS -->
    <style>
        /* Marquee wrapper */
        .marquee {
            width: 100%; /* Full width */
            overflow: hidden; /* Hide overflowing content */
        }

        .marquee p {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 10s linear infinite;
        }

        /* Define the animation */
        @keyframes marquee {
            0% {
                transform: translateX(100%); /* Start off the screen from the right */
            }
            100% {
                transform: translateX(-100%); /* Move all the way to the left */
            }
        }
    </style>
</div>
