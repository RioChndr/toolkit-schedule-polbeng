Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/lunar64"

  config.vm.provider "virtualbox" do |vb|
    vb.memory = 1024
    vb.cpus = 1
  end

  config.vm.provision "shell", path: "provision.sh"

  config.vm.network "forwarded_port", guest: 80, host: 8080

  config.vm.synced_folder "src/", "/var/www/html", :mount_options => ["dmode=777", "fmode=666"]
end

