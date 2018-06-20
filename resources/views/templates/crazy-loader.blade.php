<div id="crazyloader" >
    <style>
        .crazyloader{
            position: fixed;
           /* background-color: #0f151d;opacity: .9;filter: Alpha(Opacity=90);*/
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99999;
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-flex-flow: row nowrap;
            -ms-flex-flow: row nowrap;
            flex-flow: row nowrap;
            -webkit-box-pack: center;
            -webkit-justify-content: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            background: none repeat scroll 0 0 #ffffff;
        }
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
    <div id="crazy" class="crazyloader">
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