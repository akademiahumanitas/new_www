# Import required variables
source ${SCRIPTS_LOCATION}/tasks/variables.sh

# Script header
source ${SCRIPTS_LOCATION}/tasks/header.sh

# Ask names and credentials
source ${SCRIPTS_LOCATION}/tasks/askvars.sh

# Get latest Humanitas version with updates and copy it over to your project
source ${SCRIPTS_LOCATION}/tasks/get-theme.sh

# Set needed file and directory permissions
source ${SCRIPTS_LOCATION}/tasks/permissions.sh

# Get and install theme dependencies, npm and devpackages
source ${SCRIPTS_LOCATION}/tasks/dependencies.sh

# Create latest Humanitas development packages for project root level (gulp paths etc.)
source ${SCRIPTS_LOCATION}/tasks/project.sh

# Clean up leftover development files from Humanitas
source ${SCRIPTS_LOCATION}/tasks/cleanups.sh

# Add media folder, generate README.md for project etc.
source ${SCRIPTS_LOCATION}/tasks/additions.sh
