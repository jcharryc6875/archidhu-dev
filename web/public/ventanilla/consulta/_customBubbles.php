<?php
    include_once(dirname(__FILE__).'/../../../../lib/helper/ModalHelper.php');
?>
<div class="container-fluid">
    <div class="timeline-container">
        
        <div class="timeline-wrapper">
            <!-- Línea de fondo -->
            <div class="timeline-line"></div>
            
            <!-- Línea de progreso -->
            <div class="timeline-progress" id="timelineProgress"></div>
            
            <!-- Fase 1 -->
            <?php
                $bubble_class = 'pending';
                $item = 0;
                foreach($array_vista_data['status_publicacion'] as $clave => $valor)
                    {
                        //echo $clave . '   ----   ';
                        $bubble_class = ($valor <= $array_vista_data['current_process_id']) ? 'completed' : 'pending';
                        ?>

                        <div class="<?php echo 'timeline-phase phase-' . $bubble_class; ?>" data-phase="1">
                        <!-- <div class="timeline-phase phase-completed" data-phase="1"> -->
                            <div class="phase-oval">
                                <?php echo $clave; ?>
                            </div>
                            <div class="phase-description">
                                <?php 
                                    if(isset($array_vista_data['warning']))
                                    {
                                        echo $array_vista_data['warning'];
                                    }
                                ?>
                            </div>
                            <div class="phase-number"><?php echo ++$item; ?></div>
                        </div>

                        <?php
                    }
            ?>
        </div>

    </div>
</div>