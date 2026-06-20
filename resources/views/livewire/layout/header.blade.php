<div>
    <section class="px-2 py-2 md:px-0">
        <div class="container items-center max-w-6xl px-8 mx-auto xl:px-5">
            <div class="flex flex-wrap items-center sm:-mx-3">
                <!-- Typing Text -->
                <div class="w-full md:w-1/2 md:px-3">
                    <div class="w-full pb-6 space-y-6 sm:max-w-md lg:max-w-lg md:space-y-4 lg:space-y-8 xl:space-y-9 sm:pr-5 lg:pr-0 md:pb-0">
                        <div
                            x-data="{
                            text: '',
                            textArray: ['Beautiful Pages to', 'Tell Your Story!'],
                            textIndex: 0,
                            charIndex: 0,
                            typeSpeed: 110,
                            cursorSpeed: 550,
                            pauseEnd: 1500,
                            pauseStart: 20,
                            direction: 'forward',
                        }"
                            x-init="$nextTick(() => {
                            let typingInterval = setInterval(startTyping, $data.typeSpeed);

                            function startTyping(){
                                let current = $data.textArray[$data.textIndex];

                                if($data.charIndex > current.length){
                                        $data.direction = 'backward';
                                        clearInterval(typingInterval);

                                        setTimeout(function(){
                                            typingInterval = setInterval(startTyping, $data.typeSpeed);
                                        }, $data.pauseEnd);
                                }

                                $data.text = current.substring(0, $data.charIndex);

                                if($data.direction == 'forward')
                                {
                                    $data.charIndex += 1;
                                }
                                else
                                {
                                    if($data.charIndex == 0)
                                    {
                                        $data.direction = 'forward';
                                        clearInterval(typingInterval);
                                        setTimeout(function(){
                                            $data.textIndex += 1;
                                            if($data.textIndex >= $data.textArray.length)
                                            {
                                                $data.textIndex = 0;
                                            }
                                            typingInterval = setInterval(startTyping, $data.typeSpeed);
                                        }, $data.pauseStart);
                                    }
                                    $data.charIndex -= 1;
                                }
                            }

                            setInterval(function(){
                                if($refs.cursor.classList.contains('hidden'))
                                {
                                    $refs.cursor.classList.remove('hidden');
                                }
                                else
                                {
                                    $refs.cursor.classList.add('hidden');
                                }
                            }, $data.cursorSpeed);
                        })"
                            class="flex items-center justify-center mx-auto text-center max-w-7xl">
                            <div class="relative flex items-center justify-center h-auto">
                                <p class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-4xl lg:text-5xl xl:text-6xl" x-text="text"></p>
                                <span class="absolute right-0 w-2 -mr-2 bg-black h-3/4" x-ref="cursor"></span>
                            </div>
                        </div>
                        <p class="mx-auto text-base text-gray-500 sm:max-w-md lg:text-xl md:max-w-3xl">It's never been easier to build beautiful websites that convey your message and tell your story.</p>
                        <div class="relative flex flex-col sm:flex-row sm:space-x-4">
                            <a href="#_" class="flex items-center w-full px-6 py-3 mb-3 text-lg text-white bg-indigo-600 rounded-md sm:mb-0 hover:bg-indigo-700 sm:w-auto" data-primary="indigo-600" data-rounded="rounded-md">
                                Try It Free
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                            <a href="#_" class="flex items-center px-6 py-3 text-gray-500 bg-gray-100 rounded-md hover:bg-gray-200 hover:text-gray-600" data-rounded="rounded-md">
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Image Carousel with Progress Bar -->
                <div class="w-full md:w-1/2">
                    <div x-data="{
                    images: ['{{ asset('public/images/curtain1.svg') }}', '{{ asset('public/images/curtain2.svg') }}', '{{ asset('public/images/curtain3.svg') }}'],
                    currentImage: 0,
                    progress: 0,
                    changeImage() {
                        this.progress = 0;
                        this.currentImage = (this.currentImage + 1) % this.images.length;
                        this.progressInterval = setInterval(() => {
                            this.progress += 1;
                            if (this.progress >= 100) {
                                clearInterval(this.progressInterval);
                                this.changeImage();
                            }
                        }, 50);
                    }
                }"
                         x-init="changeImage()">
                        <img :src="images[currentImage]" class="w-full h-auto overflow-hidden" alt="Image Carousel">

                        <div class="relative w-full h-3 mt-4 overflow-hidden rounded-full bg-neutral-100">
                            <span :style="'width:' + progress + '%'" class="absolute h-full duration-300 ease-linear bg-neutral-900"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
