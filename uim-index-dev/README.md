# uim-index-dev
## 如果没安装过nodejs,在机器上安装nodejs
```
debian/ubuntu
github地址 https://github.com/nodesource/distributions/blob/master/README.md

curl -sL https://deb.nodesource.com/setup_10.x | bash -
apt-get install -y nodejs

查看是否按照以及版本
node -v 
npm -v
```
### 以下命令需在本目录运行而不是网站根目录

## Project setup
```
如果你是新下载的网站程序并且刚装完nodejs,那么运行以下命令

npm install

并且每次更新index时都需要先运行一次这个命令
```

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build

此命令构建index页面，会在public/vuedist目录生成对应的index.html、css、js文件
将index.html改名为 index.tpl，移动到resourse/views/material 目录下

cp -u ../public/vuedist/index.html ../resources/views/material/index.tpl

当前目录下一键运行以上两个命令脚本
./createIndexTpl.sh
```

### Run your tests
```
npm run test
```

### Lints and fixes files
```
npm run lint
```

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).
