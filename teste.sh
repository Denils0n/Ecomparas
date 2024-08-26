#!/bin/bash

# Defina o intervalo de monitoramento
INTERVAL=5
LOG_FILE="monitoramento.log"
TEMP_FILE="temp_monitoramento.log"

# Limpa arquivos antigos
> $LOG_FILE
> $TEMP_FILE

# Função para calcular a média
calculate_average() {
    local file="$1"
    local total=0
    local count=0

    while IFS= read -r line; do
        if [[ "$line" =~ ^[0-9]+ ]]; then
            total=$(($total + $line))
            count=$(($count + 1))
        fi
    done < "$file"

    if [ $count -gt 0 ]; then
        echo "scale=2; $total / $count" | bc
    else
        echo "N/A"
    fi
}

# Captura o sinal de interrupção (Ctrl+C)
trap 'echo "Interrupção detectada. Finalizando monitoramento..."; mv $TEMP_FILE $LOG_FILE; exit' INT

echo "Iniciando monitoramento. Pressione Ctrl+C para parar."

while true; do
    echo "CPU e Memória - $(date)" >> $TEMP_FILE
    top -bn1 | grep "Cpu(s)" | awk '{print $2 + $4}' >> $TEMP_FILE  # Uso total de CPU
    free -m | grep Mem | awk '{print $3}' >> $TEMP_FILE  # Memória usada
    echo "--------------------------" >> $TEMP_FILE
    sleep $INTERVAL
done
