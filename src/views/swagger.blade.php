swagger: "2.0"
info:
  description: "Api Documentation"
  version: "1.0.1"
  title: "{{config('app.name')}}"
  contact:
    name: "Dennis"
    url: "https://de-jong.frl"
    email: "dennis@de-jong.frl"
basePath: "/"
securityDefinitions:
  JWT:
    type: "apiKey"
    name: "Authorization"
    in: "header"
    description: "Uses JWT auth."
security:
  JWT: []
tags: [ ]
schemes:
 - "http" 
 - "https"
paths:

@foreach($collection as $route => $methods)
  {{$route}}:
  @foreach($methods as $method)
  {{strtolower($method['method'])}}:
      tags:
       - "{{strtolower($method['prefix'])}}"
      consumes:
        - "multipart/form-data"
      produces:
        - "application/json"
      
      parameters:
@foreach($method['parameters'] as $param)
      - name: "{{addslashes($param['name'])}}"
        in: "{{$param['in']}}"
        description: "{{$param['description']}}"
        required: {{$param['required']}}
        type: "{{$param['type']}}"
@endforeach
      responses:
        200:
          description: "OK"
@endforeach

@endforeach