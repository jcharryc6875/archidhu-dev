<div class="task-execution">
    <h2>Execute Task: <?php echo $task->getTaskName() ?></h2>
    <p>Process: <?php echo $diagram->getName() ?></p>
    
    <form id="task-form" method="post" action="<?php echo url_for('bpmn/completeTask?id=' . $task->getId()) ?>">
        
        <!-- Variables Personalizadas -->
        <?php if ($task_config && isset($task_config['variables'])): ?>
            <div class="variables-section">
                <h4>Required Information</h4>
                <?php foreach ($task_config['variables'] as $variable): ?>
                    <div class="form-group">
                        <label>
                            <?php echo $variable['name'] ?>
                            <?php if ($variable['required']): ?>
                                <span class="required">*</span>
                            <?php endif; ?>
                        </label>
                        
                        <?php if ($variable['type'] === 'text'): ?>
                            <input type="text" name="variables[<?php echo $variable['name'] ?>]" 
                                   class="form-control" 
                                   <?php if ($variable['required']): ?>required<?php endif; ?>>
                        
                        <?php elseif ($variable['type'] === 'number'): ?>
                            <input type="number" name="variables[<?php echo $variable['name'] ?>]" 
                                   class="form-control" 
                                   <?php if ($variable['required']): ?>required<?php endif; ?>>
                        
                        <?php elseif ($variable['type'] === 'date'): ?>
                            <input type="date" name="variables[<?php echo $variable['name'] ?>]" 
                                   class="form-control" 
                                   <?php if ($variable['required']): ?>required<?php endif; ?>>
                        
                        <?php elseif ($variable['type'] === 'textarea'): ?>
                            <textarea name="variables[<?php echo $variable['name'] ?>]" 
                                      class="form-control" 
                                      rows="3"
                                      <?php if ($variable['required']): ?>required<?php endif; ?>></textarea>
                        
                        <?php elseif ($variable['type'] === 'select'): ?>
                            <select name="variables[<?php echo $variable['name'] ?>]" 
                                    class="form-control"
                                    <?php if ($variable['required']): ?>required<?php endif; ?>>
                                <option value="">Select...</option>
                                <?php 
                                $options = explode(',', $variable['options']);
                                foreach ($options as $option): 
                                    $option = trim($option);
                                ?>
                                    <option value="<?php echo $option ?>"><?php echo $option ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Acciones Personalizadas -->
        <div class="actions-section">
            <h4>Actions</h4>
            <button type="submit" name="action" value="complete" class="btn btn-success">
                Complete Task
            </button>
            <button type="submit" name="action" value="save" class="btn btn-primary">
                Save Progress
            </button>
            <a href="<?php echo url_for('bpmn/taskList') ?>" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

<style>
.task-execution {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: white;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.required {
    color: red;
}
.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 3px;
    box-sizing: border-box;
}
.actions-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}
.btn {
    padding: 10px 20px;
    margin-right: 10px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}
.btn-success { background: #28a745; color: white; }
.btn-primary { background: #007cba; color: white; }
.btn-secondary { background: #6c757d; color: white; }
</style>