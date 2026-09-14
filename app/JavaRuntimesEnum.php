<?php

namespace App;

enum JavaRuntimesEnum: string
{
    case JRE_LEGACY = 'jre-legacy';
    case JAVA_RUNTIME_ALPHA = 'java-runtime-alpha';
    case JAVA_RUNTIME_BETA = 'java-runtime-beta';
    case JAVA_RUNTIME_GAMMA = 'java-runtime-gamma';
    case JAVA_RUNTIME_DELTA = 'java-runtime-delta';
    case JAVA_RUNTIME_EPSILON = 'java-runtime-epsilon';

    public function label(): string
    {
        return match ($this) {
            self::JRE_LEGACY => 'Java 8 (jre-legacy)',
            self::JAVA_RUNTIME_ALPHA => 'Java 16 (java-runtime-alpha)',
            self::JAVA_RUNTIME_BETA => 'Java 17 (java-runtime-beta)',
            self::JAVA_RUNTIME_GAMMA => 'Java 17 (java-runtime-gamma)',
            self::JAVA_RUNTIME_DELTA => 'Java 21 (java-runtime-delta)',
            self::JAVA_RUNTIME_EPSILON => 'Java 25 (java-runtime-epsilon)',
        };
    }
}
