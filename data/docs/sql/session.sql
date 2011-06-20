CREATE TABLE sessiondata (
    id     varchar(32) NOT NULL,
    expiry int(10),
    data   text,
    PRIMARY KEY (id)
);