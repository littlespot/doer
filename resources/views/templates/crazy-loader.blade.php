<div id="crazyloader" class="modal" style="display: block;background-color: #0f151d;opacity: .9;filter: Alpha(Opacity=90); ">
    <style>

        #crazy .original
        {
            position: absolute;
            top: 35%;
            left: 50%;
        }

        #crazy .reverse
        {
            position: absolute;
            top: 35%;
            left: 50%;
            transform: rotate(180deg);
        }

      #crazy  .square
        {
            width: 6px;
            height: 23px;
            border-radius: 9px;
            background: rgba(0, 255, 255, 1);
            display: block;
            margin: 13px;
            animation: moveAround 2.1s ease infinite;
        }

      #crazy  .reverse .square
        {
            background: rgba(30, 255, 110, 1);
        }


       #crazy .top
        {
            position: absolute;
            top: 35%;
            left: 50%;
            transform: rotate(-90deg);
        }

        #crazy .top .square
        {
            background: rgba(255, 0, 0, 1);
        }

      #crazy  .bottom
        {
            position: absolute;
            top: 35%;
            left: 50%;
            transform: rotate(90deg);
        }

       #crazy .bottom .square
        {
            background: rgba(255, 255, 0, 1);
        }

        @keyframes moveAround
        {
            0% { transform: translateX(0); }
            35% { transform: translateX(400%) translateY(0%) rotate(0deg); }
            50% { transform: translateX(500%) translateY(50%) rotate(45deg); }
            85% { transform: translateX(30%) translateY(-50%) rotate(45deg); }
            100% { transform: translateX(0%) translateY(0%) rotate(0deg); }
        }
    </style>
    <div id="crazy" class="modal-dialog">
        <div class="top">
            <div class="square">
                <div class="square">
                    <div class="square">
                        <div class="square">
                            <div class="square">
                                <div class="square">
                                    <div class="square">
                                        <div class="square">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom">
            <div class="square">
                <div class="square">
                    <div class="square">
                        <div class="square">
                            <div class="square">
                                <div class="square">
                                    <div class="square">
                                        <div class="square">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="original">
            <div class="square">
                <div class="square">
                    <div class="square">
                        <div class="square">
                            <div class="square">
                                <div class="square">
                                    <div class="square">
                                        <div class="square">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="reverse">
            <div class="square">
                <div class="square">
                    <div class="square">
                        <div class="square">
                            <div class="square">
                                <div class="square">
                                    <div class="square">
                                        <div class="square">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>