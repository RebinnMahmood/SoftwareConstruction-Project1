pipeline {
    agent any

    environment {
        DOCKER_IMAGE = "rebinmahmood/shifatime"
        DOCKER_TAG = "latest"
        JMETER_HOME = "C:\\apache-jmeter-5.6.3"
        TEST_PLAN = "${WORKSPACE}\\test-plan.jmx"
        RESULTS_FILE = "${WORKSPACE}\\results.jtl"
    }

    stages {

        stage('Checkout') {
            steps {
                echo 'Cloning project from GitHub...'
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                echo 'Building Docker image for ShifaTime...'
                bat 'docker build -t %DOCKER_IMAGE%:%DOCKER_TAG% .'
            }
        }

        stage('Push to Docker Hub') {
            steps {
                echo 'Pushing Docker image to Docker Hub...'
                withCredentials([usernamePassword(
                    credentialsId: 'dockerhub-id',
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    bat 'docker login -u %DOCKER_USER% -p %DOCKER_PASS%'
                    bat 'docker push %DOCKER_IMAGE%:%DOCKER_TAG%'
                }
            }
        }

        stage('Deploy') {
            steps {
                echo 'Deploying ShifaTime application...'
                bat 'docker compose up -d --build'
            }
        }

        stage('Performance Test') {
            steps {
                echo 'Running JMeter Performance Tests...'
                bat '"%JMETER_HOME%\\bin\\jmeter.bat" -n -t "%TEST_PLAN%" -l "%RESULTS_FILE%"'
            }
            post {
                always {
                    perfReport sourceDataFiles: 'results.jtl'
                }
            }
        }

    }

    post {
        success {
            echo 'Pipeline completed successfully! ShifaTime is deployed and tested.'
        }
        failure {
            echo 'Pipeline failed. Please check the logs above.'
        }
    }
}
