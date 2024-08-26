#!/bin/bash

LOG_FILE="monitoramento.log"

# Função para calcular a variação e a média
analyze_log() {
    local file="$1"

    echo "Analisando log: $file"

    # Filtra o uso de CPU e memória
    cpu_usage=$(grep "CPU e Memória" $file | grep "Cpu(s)" | awk '{print $2 + $4}')
    mem_usage=$(grep "CPU e Memória" $file | grep "Mem:" | awk '{print $3}')

    # Verifica se as variáveis estão vazias e inicializa
    if [ -z "$cpu_usage" ]; then
        cpu_usage="0"
    fi

    if [ -z "$mem_usage" ]; then
        mem_usage="0"
    fi

    # Calcula a média
    cpu_avg=$(echo "$cpu_usage" | awk '{s+=$1} END {print s/NR}')
    mem_avg=$(echo "$mem_usage" | awk '{s+=$1} END {print s/NR}')

    echo "Média do uso da CPU: $cpu_avg"
    echo "Média da memória usada: $mem_avg"

    # Calcula a variação
    cpu_min=$(echo "$cpu_usage" | sort -n | head -n 1)
    cpu_max=$(echo "$cpu_usage" | sort -n | tail -n 1)
    mem_min=$(echo "$mem_usage" | sort -n | head -n 1)
    mem_max=$(echo "$mem_usage" | sort -n | tail -n 1)

    cpu_var=$(echo "$cpu_max - $cpu_min" | bc)
    mem_var=$(echo "$mem_max - $mem_min" | bc)

    echo "Variação do uso da CPU: $cpu_var"
    echo "Variação da memória usada: $mem_var"
}

# Executa a análise
analyze_log $LOG_FILE
