//OT.setLogLevel(OT.DEBUG);
var TBInstructor = {
    session: null,
    publisher: null,
    streams: {},
    activeStreams: {},
    subscribers: {},
    streamQueue: [],
    intervalRef: null,
    sinkId: '',
    init: function () {
        var bc = OT.checkSystemRequirements();
        if (bc != 1) {
            alert('Your browser is not supported, please use Chrome or Firefox instead.');
            window.location.href = '/instructor/classes';
            return;
        } else {
            TBInstructor.checkForceClassEnd();
            TBInstructor.spinners();
            TBInstructor.volumeControl();
            TBInstructor.session = OT.initSession(apiKey, sessionId);
            TBInstructor.getUserMedia();
            TBInstructor.sessionOn();
        }
    },
    setCycleValues: function () {
        streamLimit = $('#new_max_displayed').val();
        streamTimeLimit = $('#new_rotation_sec').val();
        rotateAtATime = $('#new_at_a_time').val();
    },
    volumeControl: function () {
        var range = $('.volumeControl');
        var max = range.map(function () {
            return this.max;
        }).get();
        range.rangeslider({
            polyfill: false,
            onSlide: function (position, value) { //onSlideEnd
                $.each(TBInstructor.subscribers, function(key, subscriber) {
                    subscriber.setAudioVolume(value);
                });
            }
        });
    },
    spinners: function () {
        $('#spinner-1-members').spinner({
            spin: function(event, ui) {
                var newval = ui.value;
                var oldval = streamLimit;
                streamLimit = newval;

                if (newval > oldval) {
                    TBInstructor.rotationIncrease(newval);
                } else if (newval < oldval) {
                    TBInstructor.rotationDecrease(newval);
                }

                clearInterval(TBInstructor.intervalRef);
                TBInstructor.intervalRef = setInterval(TBInstructor.rotationCycle, streamTimeLimit);
            }
        });
        $('#spinner-2-seconds').spinner({
            spin: function(event, ui) {
                streamTimeLimit = ui.value * 1000;
                clearInterval(TBInstructor.intervalRef);
                TBInstructor.intervalRef = setInterval(TBInstructor.rotationCycle, streamTimeLimit);
            }
        });
    },
    changeAudioOutput: function (dest) {
        TBInstructor.sinkId = dest;
    },
    getUserMedia: function () {
        navigator.mediaDevices.getUserMedia({audio: true, video: true}).then(function (mediaStream) {
            var selects = {
                audioInput: $('#audioInput')[0],
                videoInput: $('#videoInput')[0]
            };

            $('#av-settings-webcam').show();
            $('#av-settings-mic').show();

            var has_audio_output = false;
            navigator.mediaDevices.enumerateDevices().then(function (devices) {
                devices.forEach(function (device) {
                    if (device.kind.toLowerCase() == 'audiooutput') {
                        has_audio_output = true;

                        var option = document.createElement("option");
                        option.value = device.deviceId;
                        option.text = device.label;
                        $('#audiooutput')[0].appendChild(option);

                        $('#av-settings-output').show();
                        $('.chosen-select').trigger('chosen:updated');
                    }
                });
            }).catch(function(err) {
                // Browser not supported
            });

            OT.getDevices(function (err, devices) {
                if (err) {
                    return;
                }

                devices.forEach(function (device) {
                    var option = document.createElement("option");
                    option.value = device.deviceId;
                    option.text = device.label;
                    selects[device.kind].appendChild(option);
                });

                $('.chosen-select').trigger('chosen:updated');
            });
        }).catch(function (error) {
            //
        });
    },
    // Called when rotation value is changed up
    makeActiveAndSendToBackOfQueue: function () {
        //console.log('flip queue ' + new Date().getTime());
        // First in the queue
        var nextInLine = TBInstructor.streamQueue[0];
        // Now that we have the stream, send them to the back of the queue
        var i = TBInstructor.streamQueue.indexOf(nextInLine);
        if (i != -1) {
            TBInstructor.streamQueue.splice(i, 1);
        }
        TBInstructor.streamQueue.push(nextInLine);
        // Finally, add them to activeStreams
        TBInstructor.activeStreams[nextInLine] = TBInstructor.streams[nextInLine];

        return nextInLine;
    },
    rotationIncrease: function (newval) {
        var streamsLength = Object.keys(TBInstructor.streams).length;
        if (newval <= streamsLength && TBInstructor.streamQueue.length > 0) {
            // Add next stream in queue, and move it to back of the queue
            TBInstructor.makeActiveAndSendToBackOfQueue();
            TBInstructor.showHideStreams();
        }
    },
    // Called when rotation value is changed down
    rotationDecrease: function (newval) {
        var activeStreamsLength = Object.keys(TBInstructor.activeStreams).length;
        if (newval < activeStreamsLength) {
            // Remove an active stream... First one in the list
            //var last = Object.keys(TBInstructor.activeStreams)[Object.keys(TBInstructor.activeStreams).length - 1];
            var first = Object.keys(TBInstructor.activeStreams)[0];

            delete TBInstructor.activeStreams[first];

            TBInstructor.showHideStreams();
        }
    },
    // Called by the timer every x seconds
    rotationCycle: function () {
        //console.log('Queue');
        //console.log(TBInstructor.streamQueue);
        //console.log('rotate ' + new Date().getTime());
        var streamsLength = Object.keys(TBInstructor.streams).length;
        // Only called if there are more streams than the limit
        if (streamsLength > streamLimit) {
            // Remove all active streams, grab first "streamLimit" number of streams from the queue, add them to activeStreams and move them to end of the queue
            ////TBInstructor.activeStreams = {};
            var first = Object.keys(TBInstructor.activeStreams)[0];

            delete TBInstructor.activeStreams[first];

            /*var lim = streamLimit;
            if (Object.keys(TBInstructor.streams).length < lim) {
                lim = Object.keys(TBInstructor.streams).length;
            }*/
            var lim = rotateAtATime;

            for (var j = 0; j < lim; j++) {
                var newone = TBInstructor.makeActiveAndSendToBackOfQueue();
                if (TBInstructor.streamQueue.length >= streamLimit) {
                    var first2 = TBInstructor.streamQueue[streamLimit - 1];
                } else {
                    var first2 = TBInstructor.streamQueue[TBInstructor.streamQueue.length - 1];
                }
                
                TBInstructor.showHideStreams(first2, newone);
            }

            //TBInstructor.showHideStreams();
        }
    },
    // Function that processes the activeStreams to determine what to show/hide
    showHideStreams: function (deletedone, newone) {
        console.log(deletedone + ' _________ ' + newone);

        if (deletedone && newone) {
            //console.log('DOM switch!');
        console.log('insert ' + deletedone + ' before ' + newone);
            $($('#stream_' + deletedone).parent()).insertBefore($('#stream_' + newone).parent());
            //$($('#stream_' + deletedone).parent().parent()).append($('#stream_' + newone).parent());
        }
        console.log(TBInstructor.streamQueue);

        for (i = 0; i < TBInstructor.streamQueue.length; i++) {
            if ((i + 1) <= streamLimit) {
                if ($('#subscribersDiv #stream_' + TBInstructor.streamQueue[i]).length > 0) {
                    // Do nothing - already shown
                    console.log('already shown ' + TBInstructor.streamQueue[i]);
                } else {
                    console.log('append! ' + TBInstructor.streamQueue[i]);
                    $('#subscribersDiv').append($('#stream_' + TBInstructor.streamQueue[i]).parent());
                    
                }
            } 
            if ((i + 1) > streamLimit) {
                if ($('#trashDiv #stream_' + TBInstructor.streamQueue[i]).length > 0) {
                    // Do nothing, already trashed
                    console.log('already trashed ' + TBInstructor.streamQueue[i]);
                } else {
                    console.log('trash!' + TBInstructor.streamQueue[i]);
                    $('#trashDiv').append($('#stream_' + TBInstructor.streamQueue[i]).parent());
                    
                }
            }
        }

        // Show/hide, send messages to hidden to mute, send messages to active to unmute
        var keys = Object.keys(TBInstructor.streams);
        keys.forEach(function (id) {
            if (TBInstructor.activeStreams[id]) {
                // Send message to unmute
                //TBInstructor.sendSignal(TBInstructor.streams[id].connection, 'unmute');
                ///////TBInstructor.subscribers[id].subscribeToAudio(true);
                ///////TBInstructor.subscribers[id].subscribeToVideo(true);

                // ==========================================================================
                /*if (TBInstructor.activeStreams[id]) {
                var subscriber = TBInstructor.session.subscribe(TBInstructor.streams[id], 'stream_' + id, {audioVolume: 50, fitMode: 'contain', height: '100%', width: '100%', style: {nameDisplayMode: 'on'}}, function (error) {
                    if (error) {
                        alert('Cannot subscribe to new joining member.');
                    }
                });
                 TBInstructor.subscribers[id] = subscriber;
                console.log('Added via rotation: ');
                console.log(TBInstructor.subscribers[id]);
                }*/
                // ==========================================================================

                //$('#stream_' + id).parent().show();
            } else {
                // Send message to mute
                //TBInstructor.sendSignal(TBInstructor.streams[id].connection, 'mute');
                ///////TBInstructor.subscribers[id].subscribeToAudio(false);
                ///////TBInstructor.subscribers[id].subscribeToVideo(false);

                // ==========================================================================
                /*if (!TBInstructor.activeStreams[id]) {
                console.log('Removed: ');
                console.log(TBInstructor.subscribers[id]);
                TBInstructor.session.unsubscribe(TBInstructor.subscribers[id]);
                }*/
                // ==========================================================================

                //$('#stream_' + id).parent().hide();
            }
        });

        OT_LayoutContainer.layout();
    },
    sendSignal: function (sto, saction) {
        TBInstructor.session.signal({
            to: sto,
            data: saction
        }, function(error) {
            if (error) {
                alert('Could not send signal to members.');
            } else {
                //
            }
        });
    },
    updatePeopleWatching: function () {
        var streams_length = Object.keys(TBInstructor.streams).length;
        $('#peopleWatching').html(streams_length);

        if (TBInstructor.sinkId.length > 0) {
            setTimeout(function() {
                $("#subscribersDiv .OT_video-element").each(function(index, element) {
                    element.setSinkId(TBInstructor.sinkId).then(function() {}).catch(function(error) {});
                });
            }, 5000);
        }
    },
    streamEntered: function (stream) {
        var streamid = stream.id;
        var divid = 'stream_' + streamid;
        var activeStreamsLength = Object.keys(TBInstructor.activeStreams).length;

        OT_LayoutContainer.addStream(divid, false);

        TBInstructor.streams[stream.id] = stream;
        //TBInstructor.streamQueue.push(streamid); // Maybe add to front instead?
        TBInstructor.streamQueue.unshift(streamid);

        // Subscribe to stream
        var subscriber = TBInstructor.session.subscribe(stream, divid, {audioVolume: 50, fitMode: 'contain', height: '100%', width: '100%', style: {nameDisplayMode: 'on'}}, function (error) {
            if (error) {
                alert('Cannot subscribe to new joining member.');
            }
        });
        TBInstructor.subscribers[streamid] = subscriber;

        console.log('Entered: ');
        console.log(subscriber);

        // If there is space, add immediately to activeStreams
        if (activeStreamsLength < streamLimit) {
            TBInstructor.activeStreams[streamid] = stream;
        }

        // Update layout and counts
        TBInstructor.updatePeopleWatching();
        TBInstructor.showHideStreams();
    },
    xxxcounter: 0,
    xxxAddStream: function () {
        TBInstructor.xxxcounter++;

        var streamid = TBInstructor.xxxcounter;
        var divid = 'stream_' + streamid;
        var activeStreamsLength = Object.keys(TBInstructor.activeStreams).length;

        OT_LayoutContainer.addStream(divid, false);

        TBInstructor.streams[streamid] = {};
        TBInstructor.streamQueue.unshift(streamid);
        //Subscribe
        TBInstructor.subscribers[streamid] = {};

        if (activeStreamsLength < streamLimit) {
            TBInstructor.activeStreams[streamid] = {};
        }

        // Update layout and counts
        TBInstructor.updatePeopleWatching();
        TBInstructor.showHideStreams();
    },
    streamExited: function (stream) {
        var streamid = stream.id;

        // Remove this stream from the queue
        TBInstructor.streamQueue = TBInstructor.streamQueue.filter(function(i) {
            return i != streamid;
        });

        // Remove stream from the DOM if it exists
        if ($('#stream_' + streamid).length > 0) {
            OT_LayoutContainer.removeStream('stream_' + streamid);
        }

        // Remove other references for this stream
        delete TBInstructor.streams[streamid];
        delete TBInstructor.subscribers[streamid];
        delete TBInstructor.activeStreams[streamid];

        // Add a replacement
        TBInstructor.makeActiveAndSendToBackOfQueue();

        TBInstructor.updatePeopleWatching();
        TBInstructor.showHideStreams();
    },
    sessionOn: function () {
        TBInstructor.session.on({
            streamCreated: function(event) {
                TBInstructor.streamEntered(event.stream);
            },
            streamDestroyed: function (event) {
                TBInstructor.streamExited(event.stream);
            }
        });
    },
    startSession: function () {
        console.log('streamLimit ' + streamLimit);
        console.log('streamTimeLimit ' + streamTimeLimit);
        console.log('rotateAtATime ' + rotateAtATime);
        var options = {name: instructorName, audioSource: $('#audioInput').val(), videoSource: $('#videoInput').val(), resolution: '1280x720'};

        TBInstructor.publisher = OT.initPublisher('myPublisherDiv', options, function (error) {
            if (error) {
                alert('Cannot initialise publisher.');
                return;
            }

            $('#choose-file').hide();
            $('#live').show();
            OT_LayoutContainer.init('subscribersDiv');

            TBInstructor.session.connect(token, function (error) {
                if (error) {
                    alert('Cannot connect to session.');
                    return;
                }
                
                TBInstructor.session.publish(TBInstructor.publisher, function (err) {
                    if (err) {
                        alert('Cannot publish to session.');
                        return;
                    }

                    FSlive.timer(0);
                    TBInstructor.intervalRef = setInterval(TBInstructor.rotationCycle, streamTimeLimit);
                });
            });
        });
    },
    toggleMuteAll: function (th) {
        // Note: when unmuting all, only unmute active streams...
        var el = $(th);
        if (el.hasClass('active')) {
            el.removeClass('active');
            $.each(TBInstructor.subscribers, function(key, value) {
                value.subscribeToAudio(true); // Unmute
                value.setAudioVolume(100);
            });
        } else {
            el.addClass('active');
            $.each(TBInstructor.subscribers, function(key, value) {
                value.subscribeToAudio(false); // Mute
                value.setAudioVolume(0);
            });
        }
    },
    checkForceClassEnd: function () {
        var class_id = $('#class_id_for_ajax').val();
        $.ajax({type: 'POST', url: '/ajax/live/instructor-force-end',
            complete: function (transport) {
                var resp = transport.responseText;
                if (resp == 'Y') {
                    TBInstructor.endSession(class_id);
                } else {
                    setTimeout(TBInstructor.checkForceClassEnd, 60000);
                }
            },
            data: {
                '_token': $('#ajax_csrf_token').val(),
                'id': class_id
            }
        });
    },
    endSession: function (cid) {
        $.ajax({type: 'POST', url: '/ajax/live/instructor-ends-class',
            complete: function (transport) {
                TBInstructor.session.disconnect();
                var resp = transport.responseText;
                window.location.href = resp;
            },
            data: {
                '_token': $('#ajax_csrf_token').val(),
                'id': cid
            }
        });
    }
};

$(document).ready(TBInstructor.init);